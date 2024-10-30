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

/**
 * Created (on plugin version): 1.1.1
 * Tested (on plugin version): 1.1.1
 */


namespace Mergado\Service\Ean\Plugins;

class ProductGtinEanUpcIsbn
{
    public const PLUGIN_PATH = 'product-gtin-ean-upc-isbn-for-woocommerce/product-gtin-ean-upc-isbn-for-woocommerce.php';

    private $isActive = false;

    public function __construct()
    {
        $this->prepareData();

        if ($this->isActive()) {
            require_once wp_normalize_path(WP_PLUGIN_DIR . '/' . self::PLUGIN_PATH);
        }
    }

    private function prepareData()
    {
        $this->isActive = is_plugin_active(self::PLUGIN_PATH);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getEan($productId, $productParentId, $selectedField)
    {
        if ($this->isActive()) {
            $eanCode = wpm_get_code_gtin_by_product($productId);

            if (!$eanCode && trim($eanCode) == '') {
                $eanCode = wpm_get_code_gtin_by_product($productParentId);
            }

            return $eanCode;
        }

        return '';
    }

    public function getPluginDataForSelect(): array
    {
        return [
            'active' => $this->isActive(),
            'name' => __('Product GTIN (EAN, UPC, ISBN) for WooCommerce', 'mergado-marketing-pack'),
            'hasFields' => $this->getPluginDataForSubselect()
        ];
    }

    public function getPluginDataForSubselect(): bool
    {
        return false;
    }
}
