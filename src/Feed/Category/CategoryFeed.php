<?php

/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Feed\Category;

use DateTime;
use Exception;
use Mergado\Exception\CronRunningException;
use Mergado\Helper\TaxHelper;
use Mergado\Feed\BaseFeed;
use WP_Query;
use XMLWriter;

class CategoryFeed extends BaseFeed
{
    const FEED_NAME = 'category';
    const FEED_SECTION = 'other';

    public function __construct()
    {
        parent::__construct(
            self::FEED_NAME,
            self::FEED_SECTION,
            $this->getTotalCategories(),
            'http://www.mergado.com/ns/category/1.7',
            3000
        );
    }

    /**
     * @throws CronRunningException
     */
    public function generateXML($force = false): string
    {
        try {
            $now = new DateTime();
            $this->createNecessaryDirs();

            if($this->isFeedLocked($now) && !$force) {
                $this->logger->info('FEED LOCKED - generating process can\'t proceed', $this->logContext);
                throw new CronRunningException();
            } else {
                $this->setFeedLocked($now);
                $categoriesPerStep = $this->getItemsPerStep();

                $currentFilesCount = $this->getCurrentTempFilesCount();
                $start = $this->getStart($currentFilesCount);

                // If no temporary files, reset generating
                if ($start === 1) {
                    $this->resetFeedGenerating();
                }

                $categoryList = $this->getCategories($start, $categoriesPerStep);

                // Step generating
                if ($this->isPartial($categoriesPerStep, $categoryList)) {
                    $file = $this->tmpOutputDir . ($currentFilesCount) . '.xml';

                    $this->logger->info('Generator started - step ' . $currentFilesCount, $this->logContext);
                    $this->createXML($file, $start, $categoriesPerStep, $categoryList);
                    $this->logger->info('Generator ended - step ' . $currentFilesCount, $this->logContext);
                    $this->logger->info('Generator saved XML file - step ' . $currentFilesCount, $this->logContext);

                    $this->increaseGenerationStep();
                    $this->unlockFeed();

                    return 'stepGenerated';
                    // Normal generating
                } else if ($this->isNormal($categoriesPerStep, $categoryList)) {
                    $file = $this->xmlOutputDir . $this->getFeedFileName();

                    $this->logger->info('Generator started', $this->logContext);
                    $this->createXML($file);
                    $this->logger->info('Generator ended', $this->logContext);
                    $this->logger->info('Generator saved XML file', $this->logContext);

                    $this->unlockFeed();

                    return 'fullGenerated';
                    // Merge
                } else {
                    $this->mergeTemporaryFiles();
                    $this->unlockFeed();
                    $this->saveFullFeedGenerationCount();

                    return 'merged';
                }
            }
        } catch (CronRunningException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error('Exception during feed generation: ' . $e, $this->logContext);

            throw $e;
        }
    }

    private function createXML($file, $start = null, $limit = null, $categories = null)
    {
        if ($categories === null) {
            $categories = $this->getCategories($start, $limit);
        }

        $xml_new = new XMLWriter();
        $xml_new->openURI($file);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->startElement('CHANNEL');
        $xml_new->writeAttribute('xmlns', $this->feedVersion);

        $xml_new->startElement('LINK');
        $xml_new->text(get_home_url(get_current_blog_id()));
        $xml_new->endElement();

        $xml_new->startElement('GENERATOR');
        $xml_new->text('mergado.woocommerce.marketingpack.' . str_replace('.', '_', PLUGIN_VERSION));
        $xml_new->endElement();

        foreach ($categories as $cat) {
            $minPrice = $this->getCategoryPrice($cat->slug, 'ASC');
            $maxPrice = $this->getCategoryPrice($cat->slug, 'DESC');

            $taxRate = TaxHelper::getTaxRatesForCountry(TaxHelper::getFeedTaxCountryCode($this->logContext), '');

            if (TaxHelper::isTaxCalculated()) {
                if (TaxHelper::isTaxIncluded()) {
                    $catMinPrice = $minPrice;
                    $catMaxPrice = $maxPrice;
                } else {
                    $catMinPrice = round($minPrice * (1 + ($taxRate / 100)), 2);
                    $catMaxPrice = round($maxPrice * (1 + ($taxRate / 100)), 2);
                }
            } else {
                $catMinPrice = $minPrice;
                $catMaxPrice = $maxPrice;
            }

            if ($cat->parent !== 0) {
                $breadcrumbs = $this->getBreadcrumbs($cat->parent, $cat->name);
            } else {
                $breadcrumbs = $cat->name;
            }

            // START ITEM
            $xml_new->startElement('ITEM');


            $xml_new->startElement('CATEGORY_NAME');
            $xml_new->writeCdata($cat->name);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY');
            $xml_new->writeCdata( $breadcrumbs);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_ID');
            $xml_new->text($cat->term_id);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_URL');
            $xml_new->text(get_category_link($cat));
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_QUANTITY');
            $xml_new->text($cat->count);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_DESCRIPTION');
            $xml_new->writeCdata($cat->description);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_MIN_PRICE_VAT');
            $xml_new->text($catMinPrice);
            $xml_new->endElement();

            $xml_new->startElement('CATEGORY_MAX_PRICE_VAT');
            $xml_new->text($catMaxPrice);
            $xml_new->endElement();

            // END ITEM
            $xml_new->endElement();
        }

        $xml_new->endElement();
        $xml_new->endDocument();
        $xml_new->flush();
        unset($xml_new);
    }

    /**
     * Return max or min price in category
     *
     * SORT -> 'DESC' for MAX value
     * SORT -> 'ASC' for MIN value
     *
     * @param $slug
     * @param $sort
     * @return mixed
     */
    private function getCategoryPrice($slug, $sort)
    {
        $args = array(
            'posts_per_page' => 1,
            'post_type' => 'product',
            'orderby' => 'meta_value_num',
            'order' => $sort,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $slug,
                    'operator' => 'IN'
                )
            ),
            'meta_query' => array(
                array(
                    'key' => '_price',
                )
            )
        );

        $loop = new WP_Query($args);

        return get_post_meta($loop->posts[0]->ID, '_price', true);
    }

    /**
     * Return breadcrumbs for category feed
     *
     * @param $id
     * @param $name
     * @return string
     */
    private function getBreadcrumbs($id, $name): string
    {
        $term = get_term_by('id', $id, 'product_cat');

        if ($term->parent != 0) {
            $newName = $term->name . ' | ' . $name;
            return $this->getBreadcrumbs($term->parent, $newName);
        }

        return $term->name . ' | ' . $name;
    }

    /*******************************************************************************************************************
     * MERGE
     *******************************************************************************************************************/

    /**
     * Merge files, create XML and delete temporary files
     */
    protected function mergeTemporaryFiles(): bool
    {
        $this->logger->info('Merging XML files', $this->logContext);
        return parent::mergeTemporaryFiles();
    }

    /*******************************************************************************************************************
     * GET CATEGORIES
     *******************************************************************************************************************/

    public function getCategories($start, $stepProducts)
    {
        return get_terms(
            [
                'taxonomy' => 'product_cat',
                'offset' => ($start - 1) * $stepProducts,
                'number' => $stepProducts,
                'hide_empty' => true
            ]
        );
    }

    public function getTotalCategories(): int
    {
        return count(get_terms(
            [
                'taxonomy' => 'product_cat',
                'hide_empty' => true
            ]
        ));
    }
}
