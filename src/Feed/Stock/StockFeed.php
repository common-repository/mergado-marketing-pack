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

namespace Mergado\Feed\Stock;

use DateTime;
use Exception;
use Mergado\Exception\CronRunningException;
use Mergado\Feed\BaseProductFeed;
use Mergado\Service\ProductExportService;
use Mergado\FeedVisibility\OtherFeedVisibilityService;
use WC_Product;
use XMLWriter;

class StockFeed extends BaseProductFeed
{
    public const FEED_NAME = 'stock';
    public const FEED_SECTION = 'other';

    /*******************************************************************************************************************
     * XML GENERATORS
     *******************************************************************************************************************/
    /*******************************************************************************************************************
     * HEUREKA STOCK FEED
     *******************************************************************************************************************/

    public function __construct()
    {
        parent::__construct(
            self::FEED_NAME,
            self::FEED_SECTION,
            $this->getTotalItems(),
            '',
            5000
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

            if ($this->isFeedLocked($now) && !$force) {
                $this->logger->info('FEED LOCKED - generating process can\'t proceed', $this->logContext);
                throw new CronRunningException();
            } else {
                $this->setFeedLocked($now);

                $productsPerStep = $this->getItemsPerStep();

                $currentFilesCount = $this->getCurrentTempFilesCount();
                $start = $this->getStart($currentFilesCount);

                // If no temporary files, reset generating
                if ($start === 1) {
                    $this->resetFeedGenerating();
                }

                $productsList = ProductExportService::getProducts($start, $productsPerStep);

                // Step generating
                if ($this->isPartial($productsPerStep, $productsList)) {
                    $file = $this->tmpOutputDir . ($currentFilesCount) . '.xml';

                    $this->logger->info('Generator started - step ' . $currentFilesCount, $this->logContext);
                    $this->createXML($file, $start, $productsPerStep, $productsList);
                    $this->logger->info('Generator ended - step ' . $currentFilesCount, $this->logContext);
                    $this->logger->info('Generator saved XML file - step ' . $currentFilesCount, $this->logContext);

                    $this->increaseGenerationStep();
                    $this->unlockFeed();

                    return 'stepGenerated';
                    // Normal generating
                } else if ($this->isNormal($productsPerStep, $productsList)) {
                    $file = $this->xmlOutputDir . $this->getFeedFileName();

                    $this->logger->info('Stock feed generator started');
                    $this->createXML($file);
                    $this->logger->info('Stock feed generator ended');
                    $this->logger->info('Stock feed generator saved XML file');

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

    private function createXML($file, $start = null, $limit = null, $products = null): void
    {
        if ($products === null) {
            $products = ProductExportService::getProducts($start, $limit);
        }

        $xml_new = new XMLWriter();
        $xml_new->openUri($file);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->startElement('item_list');


        foreach ($products as $i) {
            /** @var WC_Product $productObject */
            $productObject = $i['productObject'];
            /** @var WC_Product $parentObject */
            $parentObject = $i['parentProduct'];

            $otherFeedVisibilityService = OtherFeedVisibilityService::getInstance();
            $productVisibility = $otherFeedVisibilityService->isProductVisibilityEnabledForExport($productObject, $parentObject);

            // If product not enabled for export ... skip
            if ($productVisibility === false) {
                continue;
            }

            if ($productObject->is_type('simple')) {
                $qty = $productObject->get_stock_quantity();
                $stockStatus = $productObject->get_stock_status() == 'instock';

                if ($qty <= 0 && !$stockStatus) {
                    continue;
                } elseif ($stockStatus) {
                    if ($qty <= 0) {
                        $qty = 1; // If product doesn't have stock managment NULL is returned
                    }

                    $xml_new->startElement('item');
                    $xml_new->writeAttribute('id', $productObject->get_id());

                    $xml_new->startElement('stock_quantity');
                    $xml_new->text($qty);
                    $xml_new->endElement();

                    $xml_new->endElement();
                }

            } elseif ($productObject->is_type('variable')) {
                $variations = $productObject->get_available_variations();

                if ($variations != []) {
                    foreach ($variations as $variation) {
                        $qty = max($variation['max_qty'], $variation['min_qty']);
                        $stockStatus = $variation['is_in_stock'];

                        if ($qty <= 0 && !$stockStatus) {
                            continue;
                        } elseif ($stockStatus) {
                            $xml_new->startElement('item');
                            $xml_new->writeAttribute('id', $variation['variation_id']);
                            $xml_new->startElement('stock_quantity');
                            $xml_new->text($qty);
                            $xml_new->endElement();

                            $xml_new->endElement();
                        }
                    }
                }
            }
        }

        $xml_new->endElement();
        $xml_new->endDocument();
        $xml_new->flush();
        unset($xml_new);
    }

    /*******************************************************************************************************************
     * FEED OPTIONS
     *******************************************************************************************************************/

    /**
     * Merge files, create XML and delete temporary files
     */
    protected function mergeTemporaryFiles(): bool
    {
        $storage = $this->xmlOutputDir . $this->getFeedFileName();
        $tmpShopDir = $this->tmpOutputDir;

        $this->logger->info('Merging XML files', $this->logContext);

        $xmlstr = '<item_list>';

        foreach (glob($tmpShopDir . '*.xml') as $file) {
            $xml = simplexml_load_file($file);
            foreach ($xml as $item) {
                $xmlstr .= $item->asXml();
            }
        }

        $xmlstr .= '</item_list>';

        $xml_new = new XMLWriter();

        $xml_new->openURI($storage);
        $xml_new->startDocument('1.0', 'UTF-8');
        $xml_new->writeRaw($xmlstr);
        $xml_new->endDocument();

        $this->logger->info('Feed merged. XML created.', $this->logContext);

        $this->deleteTemporaryFiles();

        return true;
    }

    public function getTotalItems()
    {
        $productsPerRun = $this->getItemsPerStep();
        $lastRunIterationCount = $this->getFullFeedGenerationCount();

        $totalProducts = $this->getTotalProducts($productsPerRun, $lastRunIterationCount);

        if ($totalProducts == 0) {
            $totalProducts = (int)wp_count_posts('product')->publish;
        }

        return $totalProducts;
	}

    public function getDataForTemplates(): array
    {
        $result = parent::getDataForTemplates();

        $result['createExportInMergadoUrl'] = false;

        return $result;
    }
}
