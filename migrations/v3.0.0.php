<?php

use Mergado\Service\Ean\Plugins\CeskeSluzby;
use Mergado\Service\Ean\Plugins\EanForWoocommerce;
use Mergado\Service\Ean\Plugins\ProductGtinEanUpcIsbn;
use Mergado\Service\Ean\Plugins\WooAddGtin;
use Mergado\Service\Ean\Plugins\WpssoWcMetadata;
use Mergado\Service\Ean\EanService;

if (!class_exists('MergadoMigration_3_0_0')) {
    class MergadoMigration_3_0_0
    {
        public function __construct()
        {
        }


        public function execute()
        {
            $token = get_option('mmp_token');

            if ($token !== NULL && $token !== '' && $token) {
                if (is_multisite()) {
                    $currentBlogId = get_current_blog_id();
                } else {
                    $currentBlogId = 0;
                }

                $this->setWizardFinishedIfFeedExist($currentBlogId, $token);
                $this->setDefaultEANIfNotSet();
            }
        }

        private function setWizardFinishedIfFeedExist($currentBlogId, $token)
        {
            if (file_exists(__MERGADO_XML_DIR__ . $currentBlogId . '/' . 'products_' . $token . '.xml')) {
                update_option('mmp-wizard-finished-product', 1);
            }

            if (file_exists(__MERGADO_XML_DIR__ . $currentBlogId . '/' . 'stock_' . $token . '.xml')) {
                update_option('mmp-wizard-finished-stock', 1);
            }

            if (file_exists(__MERGADO_XML_DIR__ . $currentBlogId . '/' . 'category_' . $token . '.xml')) {
                update_option('mmp-wizard-finished-category', 1);
            }

            update_option('mergado-feed-products-default-step', 1500);
            update_option('mergado-feed-category-default-step', 3000);
            update_option('mergado-feed-stock-default-step', 5000);

            //File not exist and field is empty
            update_option('mergado-feed-import-default-step', 3000);
        }

        private function setDefaultEANIfNotSet()
        {
            $services = [
                EanService::PRODUCT_GTIN_EAN_UPC_ISBN => new ProductGtinEanUpcIsbn(),
                EanService::WOO_ADD_GTIN => new WooAddGtin(),
                EanService::EAN_FOR_WOO => new EanForWoocommerce(),
                EanService::WPSSO_WC_METADATA => new WpssoWcMetadata(),
                EanService::CESKE_SLUZBY => new CeskeSluzby(),
            ];

            foreach ($services as $service => $object) {
                $alreadySet = get_option(EanService::EAN_PLUGIN, false);

                if ($alreadySet === false) {
                    if ($object->isActive()) {
                        update_option(EanService::EAN_PLUGIN, $service);

                        $subselectData = $object->getPluginDataForSubselect();

                        if (!$subselectData) {
                            update_option(EanService::EAN_PLUGIN_FIELD, $this->array_key_first($subselectData));
                        }

                        break;
                    }
                }
            }
        }

        private function array_key_first(array $arr)
        {
            foreach ($arr as $key => $unused) {
                return $key;
            }
            return NULL;
        }
    }
}

$migration = new MergadoMigration_3_0_0();
$migration->execute();
