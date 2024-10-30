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

namespace Mergado\Feed\Product;

use DateTime;
use DOMDocument;
use DOMException;
use Exception;
use Mergado\Exception\CronRunningException;
use Mergado\External\CurrencySwitcherForWoocomerce;
use Mergado\Feed\BaseProductFeed;
use Mergado\Helper\TaxHelper;
use Mergado\Service\ProductExportService;
use Mergado\Service\Ean\EanService;
use Mergado\FeedVisibility\ProductFeedVisibilityService;
use WC_Product;

class ProductFeed extends BaseProductFeed
{
    const FEED_NAME = 'product';
    const FEED_SECTION = 'product';

    public function __construct()
    {
        parent::__construct(
            self::FEED_NAME,
            self::FEED_SECTION,
            $this->getTotalItems(),
            'http://www.mergado.com/ns/1.10',
            1500
        );

        $this->currentGenerationStepDbName = $this->getCurrentGenerationStepDbName();
        $this->fullFeedStepCountDbName = $this->getFullFeedStepCountDbName();
        $this->loweredItemCountPerStepDbName = $this->getLoweredItemCountPerStepDbName();
        $this->defaultItemCountPerStepDbName = $this->getDefaultItemCountPerStepDbName();
        $this->userItemCountPerStepDbName = $this->getUserItemCountPerStepDbName();
    }

    /*******************************************************************************************************************
     * XML GENERATORS
     ******************************************************************************************************************/

     /**
     * @throws CronRunningException|DOMException
      */
    public function generateXml($force = false): string
    {
        try {
            CurrencySwitcherForWoocomerce::algSwitcherDisable();

            $now = new DateTime();

            $this->createNecessaryDirs();

            if ($this->isFeedLocked($now) && !$force) {
                $this->logger->info('FEED LOCKED - generating process can\'t proceed', $this->logContext);
                CurrencySwitcherForWoocomerce::algSwitcherEnable();
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
                    $xml = $this->createXML($start, $productsPerStep, $productsList);
                    $this->logger->info('Generator ended - step ' . $currentFilesCount, $this->logContext);
                    $xml->save($file);
                    $this->logger->info('Generator saved XML file - step ' . $currentFilesCount, $this->logContext);

                    $this->increaseGenerationStep(); //TODO: Not necessary to increase and save every time, just take number of files before merge, same for percentage.. take files
                    $this->unlockFeed();
                    CurrencySwitcherForWoocomerce::algSwitcherDisable();

                    return 'stepGenerated';
                    // Common generating
                } else if ($this->isNormal($productsPerStep, $productsList)) {
                    $file = $this->xmlOutputDir . $this->getFeedFileName();

                    $this->logger->info('Generator started', $this->logContext);
                    $xml = $this->createXML();
                    $this->logger->info('Generator ended', $this->logContext);
                    $xml->save($file);
                    $this->logger->info('Generator saved XML file', $this->logContext);

                    $this->unlockFeed();
                    CurrencySwitcherForWoocomerce::algSwitcherDisable();

                    return 'fullGenerated';
                    // Merge temporary files
                } else {
                    $this->mergeTemporaryFiles();

                    $this->unlockFeed();
                    $this->saveFullFeedGenerationCount();
                    CurrencySwitcherForWoocomerce::algSwitcherDisable();

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


    /**
     * @param $start
     * @param $limit
     * @param null $products
     * @return DOMDocument
     * @throws DOMException
     */
    public function createXML($start = null, $limit = null, $products = null): DOMDocument
    {
        global $wpdb;

        $xml = new DOMDocument('1.0', 'UTF-8');
        $channel = $xml->createElement('CHANNEL');
        $channel->setAttribute('xmlns', 'http://www.mergado.com/ns/1.10');
        $channel->appendChild($xml->createElement('link', get_home_url(get_current_blog_id())));
        $channel->appendChild($xml->createElement('generator', 'mergado.woocommerce.marketingpack.' . str_replace('.', '_', PLUGIN_VERSION)));

        $currency = $wpdb->get_col($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'woocommerce_currency'));
        $defaults['currency'] = array_pop($currency);

        //Used units
        $weightUnit = get_option('woocommerce_weight_unit');
        $sizeUnit = get_option('woocommerce_dimension_unit');

        if ($products === null) {
            $products = ProductExportService::getProducts($start, $limit);
        }

        $eanClass = new EanService();

        foreach ($products as $k => $v) {
            $productFeedItem = new ProductFeedItem($v['type']);

            $parentId = $v['parentProductId'];
            /** @var WC_Product $productObject */
            $productObject = $v['productObject'];
            /** @var WC_Product $parentObject */
            $parentObject = $v['parentProduct'];

            $productFeedVisibilityService = ProductFeedVisibilityService::getInstance();
            $productVisibility = $productFeedVisibilityService->isProductVisibilityEnabledForExport($productObject, $parentObject);

            // If product not enabled for export ... skip
            if ($productVisibility === false) {
                continue;
            }

            $productFeedItem->setVisibility($productVisibility['visibility']);
            $productFeedItem->setCatalogVisibility($productVisibility['catalogVisibility']);

            if (!$this->is_product_type($v['type'], 'variable')) {
                $posts = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID=%s", $v['id']));

                // Why following check exists?? -------
                if (isset($posts[0])) {
                    $post = $posts[0];
                } else {
                    continue;
                }
                // ------------------------------------

                //Product parameters
                $productAttributes = $productObject->get_attributes();

                $params = $this->findParams($productObject, $productAttributes, $parentObject);

                $productName = $this->getProductName($v, $params['itemParams']);

                $productFeedItem->setItemId($v['id']);

                $stock = $this->getStockStatus($v['stock_status']);
                $productFeedItem->setAvailability($stock);

                // adresa kde je product
                $url = get_the_permalink($v['id']);

                $productFeedItem->setUrl(htmlspecialchars($url));
                $productFeedItem->setNameExact($productName);
                $productFeedItem->setCurrency($defaults['currency']);

                //PRODUCT PRICES
                $productTaxClass = $productObject->get_tax_class();
                $mergadoFeedTaxRate = TaxHelper::getTaxRatesForCountry(TaxHelper::getFeedTaxCountryCode($this->logContext), $productTaxClass); // Fill in country code from settings
                $taxRateCoefficient = ($mergadoFeedTaxRate / 100) + 1;

                if (wc_tax_enabled()) { // taxes counting enabled
                    $wc_priceWithoutTax = wc_get_price_excluding_tax($productObject); // price that count with discounts
                    $wc_priceRegularWithoutTax = wc_get_price_excluding_tax($productObject, ['qty' => 1, 'price' => $productObject->get_regular_price()]);

                    // Filter should return array of prices [ 'priceWithoutTax' => $priceWithoutTax (regular or sale price), 'regularPriceWithoutTax' => $regularPrice (regular price) ]
                    $hookPrices = apply_filters('mergado_product_feed__product_prices__taxes_enabled', ['priceWithoutTax' => $wc_priceWithoutTax, 'regularPriceWithoutTax' => $wc_priceRegularWithoutTax], $v['id'], $productObject);

                    if ($hookPrices) {
                        $wc_priceWithoutTax = $hookPrices['priceWithoutTax'];
                        $wc_priceRegularWithoutTax = $hookPrices['regularPriceWithoutTax'];
                    }

                    $priceNoVat = $wc_priceRegularWithoutTax; // price without discounts
                    $priceVat = is_numeric($wc_priceRegularWithoutTax) ? round($wc_priceRegularWithoutTax * $taxRateCoefficient, 2) : ''; // default price with VAT
                    $discountPriceNoVat = $wc_priceWithoutTax; // discounted price without VAT
                    $discountPriceVat = is_numeric($wc_priceWithoutTax) ? round($wc_priceWithoutTax * $taxRateCoefficient, 2) : ''; // discounted price with VAT

                    $productFeedItem->setVat($mergadoFeedTaxRate);
                    $productFeedItem->setPriceVat($priceVat);
                    $productFeedItem->setPrice($priceNoVat);

                    // Only if is discounted
                    if ($priceNoVat != $discountPriceNoVat) {
                        $productFeedItem->setPriceDiscount($discountPriceNoVat);
                        $productFeedItem->setPriceDiscountVat($discountPriceVat);
                    }
                } else { // taxes not counting
                    $priceVat = $productObject->get_regular_price(); // regular price
                    $discountPriceVat = $productObject->get_price(); // regular or sale price

                    // Filter should return array of prices ['regularPrice' => $priceVat (regular price), 'price' => $price (regular or sale price)]
                    $hookPrices = apply_filters('mergado_product_feed__product_prices__taxes_disabled', ['regularPrice' => $priceVat, 'price' => $discountPriceVat], $v['id'], $productObject);

                    if ($hookPrices) {
                        $priceVat = $hookPrices['regularPrice'];
                        $discountPriceVat = $hookPrices['price'];
                    }

                    $productFeedItem->setPriceVat($priceVat);

                    if ($priceVat != $discountPriceVat) {
                        $productFeedItem->setPriceDiscountVat($discountPriceVat);
                    }
                }

                $salePriceEffectiveDate = $this->getSaleDateInterval($productObject);

                if ($salePriceEffectiveDate) {
                    $productFeedItem->setSalePriceEffectiveDate($salePriceEffectiveDate);
                }

                //Take images from product if exist and assign them
                $this->setProductImages($productFeedItem, $productObject);

                // PARENT PRODUCT OBJECT
                if ($parentId) {
                    $this->setProductImages($productFeedItem, $parentObject);
                }

                // If not has main image then add placeholder
                if (!$productFeedItem->getImage()) {
                    $productFeedItem->setImage(htmlspecialchars(wc_placeholder_img_src('woocommerce_single')));
                }

                if ($params['all'] !== null) {
                    $xmlParams = [];

                    foreach ($params['all'] as $paramValue) {
                        $param = [];
                        $param['name'] = $paramValue['name'];
                        $param['value'] = $paramValue['value'];

                        $xmlParams[] = $param;
                    }

                    $productFeedItem->setParam($xmlParams);
                }

                if ($v['stock'] !== '') {
                    $productFeedItem->setStockQuantity(sprintf('%s', $v["stock"]));
                }

                if (self::is_product_type($v['type'], 'simple')) {
                    $productFeedItem->setProductno($v['sku']);
                    $productFeedItem->setDescription(apply_filters('mergado_product_feed__product_description__simple', $v['description'], $v));
                    $productFeedItem->setCategory($this->findCategory($v['category_ids']));
                    $productFeedItem->setTags($this->getTags($productObject));
                    $productFeedItem->setDescriptionShort($v['short_description']);

                    // SET EAN IF EXIST AND SELECTED PLUGIN ACTIVE
                    $eanCode = $eanClass->getEan($v, false, 'simple');

                    if ($eanCode && $eanCode !== '') {
                        $productFeedItem->setEan($eanCode);
                    }

                    if ($v["length"] != 0 && $v["width"] != 0 && $v["height"] != 0) {
                        $productFeedItem->setShippingSize(sprintf('%s x %s x %s %s', $v["length"], $v["width"], $v["height"], $sizeUnit));
                    }

                    if ($v["weight"] != 0) {
                        $productFeedItem->setShippingWeight(sprintf('%s %s', $v["weight"], $weightUnit));
                    }
                } elseif (self::is_product_type($v['type'], 'variation')) {
                    $productFeedItem->setItemgroupId($parentId);

                    $sku = '';

                    if ($v['sku'] != '') {
                        $sku = $v['sku'];
                    } else {
                        if ($parentObject && $parentObject->get_sku()) {
                            $sku = $parentObject->get_sku();
                        }
                    }

                    $productFeedItem->setProductno($sku);

                    if (isset($parentObject) && $parentObject) {
                        $csvExporter = new \WC_Product_CSV_Exporter();
                        $category_ids = $csvExporter->format_term_ids($parentObject->get_category_ids(), 'product_cat');

                        $categories = $this->findCategory($category_ids);
                    } else {
                        $categories = '';
                    }

                    $productFeedItem->setCategory($categories);
                    $productFeedItem->setTags($this->getTags($parentObject));

                    /** @var WC_Product $parentProduct */
                    if ($parentObject) {
                        $productFeedItem->setDescriptionShort($parentObject->get_short_description());
                    } else {
                        $productFeedItem->setDescriptionShort('');
                    }

                    // Description
                    if ($parentObject->get_description()) {
                        $parentDescription = $parentObject->get_description();
                    } else {
                        $parentDescription = '';
                    }

                    $descriptionContent = sprintf('%s %s', $parentDescription, $v['description']);
                    $descriptionContent = apply_filters('mergado_product_feed__product_description__variation', $descriptionContent, $products, $parentId, $v);

                    $productFeedItem->setDescription($descriptionContent);

                    // Variant description
                    $productFeedItem->setVariantDescription($v['description']);

                    // SET EAN IF EXIST AND SELECTED PLUGIN ACTIVE
                    $eanCode = $eanClass->getEan($v, $parentId, 'variation');

                    if ($eanCode && $eanCode !== '') {
                        $productFeedItem->setEan($eanCode);
                    }

                    // Variant administration in woocomerce showing MAIN PRODUCT attributes as placeholder ..
                    // so assume that customer will fill only the one he wants to change

                    $productSizes = $this->getProductSizes($v, $parentObject);

                    if ($productSizes['length'] != "" && $productSizes['width'] != "" && $productSizes['height'] != "") {
                        $productFeedItem->setShippingSize(sprintf('%s x %s x %s %s', $productSizes['length'], $productSizes['width'], $productSizes['height'], $sizeUnit));
                    }

                    if ($productSizes['weight'] != "") {
                        $productFeedItem->setShippingWeight(sprintf('%s %s', $productSizes['weight'], $weightUnit));
                    }
                }

                /**
                 * @var ProductFeedItem $customizedProductFeedItem
                 */
                $customizedProductFeedItem = apply_filters('mergado_product_feed__product', $productFeedItem, $productObject, $parentObject);

                if($itemXml = $customizedProductFeedItem->getItemXml()) {
                    $importedNode = $xml->importNode($itemXml->getElementsByTagName('ITEM')[0], true);
                    $channel->appendChild($importedNode);
                }
            }
        }

        $xml->appendChild($channel);
        return $xml;
    }


    /*******************************************************************************************************************
     * Override because of bad feed name pattern
     *******************************************************************************************************************/

    public function getFeedFileName() : string
    {
        return $this->name . 's_' . $this->token . '.xml'; //Added s
    }


    /*******************************************************************************************************************
     * MERGE XML
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
     * DATA FOR TEMPLATES
     *******************************************************************************************************************/

    public function getProductSizes($product, $parentProduct): array
    {
        $output = ['length' => '', 'width' => '', 'height' => '', 'weight' => ''];

        foreach ($output as $propertyName => $value) {
            if ($propertyName === 'length') {
                if ($product[$propertyName] !== "") {
                    $output[$propertyName] = $product[$propertyName];
                } else if ($parentProduct && $parentProduct->get_length() !== "") {
                    $output[$propertyName] = $parentProduct->get_length();
                }
            } else if ($propertyName === 'width') {
                if ($product[$propertyName] !== "") {
                    $output[$propertyName] = $product[$propertyName];
                } else if ($parentProduct && $parentProduct->get_width() !== "") {
                    $output[$propertyName] = $parentProduct->get_width();
                }
            } else if ($propertyName === 'height') {
                if ($product[$propertyName] !== "") {
                    $output[$propertyName] = $product[$propertyName];
                } else if ($parentProduct && $parentProduct->get_height() !== "") {
                    $output[$propertyName] = $parentProduct->get_height();
                }
            } else if ($propertyName === 'weight') {
                if ($product[$propertyName] !== "") {
                    $output[$propertyName] = $product[$propertyName];
                } else if ($parentProduct && $parentProduct->get_weight() !== "") {
                    $output[$propertyName] = $parentProduct->get_weight();
                }
            }
        }

        return $output;
    }

    public function getDataForTemplates(): array
    {
        $result = parent::getDataForTemplates();
        $result['createExportInMergadoUrl'] = 'https://app.mergado.com/new-project/prefill/?url=' . $this->getFeedUrl() . '&inputFormat=mergado.cz';

        return $result;
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

    public function setProductImages(ProductFeedItem $productFeedItem, $productObject)
    {
        // PRODUCT OBJECT
        $productMainImage = $productObject->get_image_id();
        $productImages = $productObject->get_gallery_image_ids();
        $galleryImages = [];

        //Main Image
        if ($productMainImage && !$productFeedItem->getImage()) {
            $productFeedItem->setImage(htmlspecialchars(wp_get_original_image_url($productMainImage)));
        } else if ($productMainImage) {
            $galleryImages[] = htmlspecialchars((wp_get_original_image_url($productMainImage)));
        }

        // If product gallery images exist
        if (count($productImages) > 0) {
            foreach ($productImages as $im) {
                // If product don't have main image use first one
                if (!$productFeedItem->getImage()) {
                    $productFeedItem->setImage(htmlspecialchars(wp_get_original_image_url($im)));
                } else {
                    $galleryImages[] = htmlspecialchars(wp_get_original_image_url($im));
                }
            }
        }


        // If gallery images exist
        if (count($galleryImages) > 0) {

            // if alternative already exists merge them
            if ($productFeedItem->getImageAlternative() && count($productFeedItem->getImageAlternative()) > 0) {
                $galleryImages = array_merge($productFeedItem->getImageAlternative(), $galleryImages);
            }

            // Add alternative images to object
            $productFeedItem->setImageAlternative($galleryImages);
        }
    }

    public function getSaleDateInterval($productObject)
    {

        $from = $productObject->get_date_on_sale_from();
        $to = $productObject->get_date_on_sale_to();

        if (!is_null($from)) {
            $from = $from->format(DateTime::ATOM);
        }

        if (!is_null($to)) {
            $to = $to->format(DateTime::ATOM);
        }

        if (is_null($from) && is_null($to)) {
            return false;
        }

        return implode('/', [$from, $to]);
    }

    /**
     * Returns used parameters with value and parent parameters that are shared between all
     *
     * Example:
     * All parent attributes:
     * ['Color' => ['red', 'blue', 'white'], 'Height' => ['1', '5', '12'], 'Width' => ['1', '5', '10']]
     *
     * Simple product - red, 5
     * ['Color' => 'red', 'Height' => '5', 'Width' => ['1', '5', '10']]
     *
     * Simple product 2 - red
     * ['Color' => ['red'], 'Height' => ['1', '5', '12'], 'Width' => ['1', '5', '10']]
     *
     * @param $product
     * @param $values
     * @param $parent
     * @return array
     */
    protected function findParams($product, $values, $parent = null): array
    {
        $itemParams = [];
        $parentParams = [];

        $usedParams = []; // For checking if already added to filter parameters that is used
        $params = [];

        if ($parent) {
            $parentValues = $parent->get_attributes();
        }

        //Default product attributes
        foreach ($values as $attrName => $attrValue) {
            $item = [];

            if ($attrValue instanceof \WC_Product_Attribute) {
                $item['name'] = wc_attribute_label($attrValue->get_name());
            } else {
                if ($parent) {
                    $attributes = $parent->get_attributes();
                    $wcProductAttribute = $attributes[$attrName];

                    $item['name'] = wc_attribute_label($wcProductAttribute->get_name());
                } else {
                    $item['name'] = wc_attribute_taxonomy_slug($attrName);
                }
            }

            $item['value'] = $product->get_attribute($attrName);

            if ($item['value'] !== '') {
                $itemParams[] = $item;
                $usedParams[] = $item['name'];
            }
        }

        //Other parameters/attributes not used in variation
        if (isset($parentValues) && $parentValues) {
            foreach ($parentValues as $attrName => $attrValue) {
                $item = [];

                if ($attrValue instanceof \WC_Product_Attribute) {
                    $item['name'] = wc_attribute_label($attrValue->get_name()); //Case //sensitive

                } else {
                    $item['name'] = wc_attribute_taxonomy_slug($attrName);
                }

                $item['value'] = $parent->get_attribute($attrName);


                if (!in_array($item['name'], $usedParams)) {
                    $parentParams[] = $item;
                }
            }
        }

        return ['all' => array_merge($itemParams, $parentParams), 'itemParams' => $itemParams, 'otherParams' => $parentParams];
    }

    protected function findCategory($categoryIds): string
    {
        $categoryGroups = explode(',', $categoryIds);

        foreach ($categoryGroups as $group) {
            $categoryTrees[] = explode(" > ", $group);
        }

        $counts = array_map('count', $categoryTrees);
        $key = array_flip($counts)[max($counts)];

        return implode(' / ', $categoryTrees[$key]);
    }

    protected function getTags(WC_Product $product): array
    {
        $tags = get_the_terms($product->get_id(), 'product_tag');

        if (!$tags) {
            return [];
        }

        $output = [];

        foreach ($tags as $tag) {
            $output[] = $tag->name;
        }

        return $output;
    }

    public static function is_product_type($typeString, $type): bool
    {
        return strpos($typeString, $type) !== false;
    }

    public function getProductName($product, $productAttributes)
    {
        $attributes = [];

        foreach ($productAttributes as $itemAttribute) {
            if (isset($itemAttribute['value']) && $itemAttribute['value'] && $itemAttribute['value'] !== '' && !wc_is_attribute_in_product_name($itemAttribute['value'], $product['name'])) {

                $attributes[] = $itemAttribute['value'];
            }
        }

        if ($attributes && count($attributes) >= 0 && self::is_product_type($product['type'], 'variation')) {
            $productName = $product['name'] . ' - ' . implode(', ', $attributes);
        } else {
            $productName = $product['name'];
        }

        return $productName;
    }

    public function getStockStatus($stock): string
    {
        if ($stock === 'backorder') {
            $stock = 'preorder';
        } else if ($stock) {
            $stock = 'in stock';
        } else {
            $stock = 'out of stock';
        }

        return $stock;
    }

    /**
     * Override of methods because bad naming convention
     * @return string
     */
    public static function getFullFeedStepCountDbName(): string
    {
        return 'mergado-feed-count';
    }

    public static function getCurrentGenerationStepDbName(): string
    {
        return 'mergado-feed-iterator';
    }

    public static function getLoweredItemCountPerStepDbName(): string
    {
        return 'mergado-feed-form-products-user';
    }

    public static function getDefaultItemCountPerStepDbName(): string
    {
        return 'mergado-feed-products-default-step';
    }

    public static function getUserItemCountPerStepDbName(): string
    {
        return 'feed-form-products';
    }
}
