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
 * Created (on plugin version): 0.5.0
 * Tested (on plugin version): 0.5.0
 */

namespace Mergado\Service\Ean\Plugins;

class WooAddGtin
{
    public const PLUGIN_PATH = 'woo-add-gtin/woocommerce-gtin.php';
    public const EAN_PRODUCT_KEY = 'hwp_product_gtin';
    public const EAN_VARIANT_KEY = 'hwp_var_gtin';

    private $isActive = false;

    public function __construct()
    {
        $this->prepareData();
    }

    private function prepareData()
    {
        $this->isActive = is_plugin_active(self::PLUGIN_PATH);
    }

    public function getEan($productId, $productParentId, $selectedField)
    {
        if ($this->isActive()) {
            // Simple
            if (!$productParentId) {
                $gtin = get_post_meta($productId, self::EAN_PRODUCT_KEY, true);
            // Variant
            } else {
                $gtin = get_post_meta($productId, self::EAN_VARIANT_KEY, true);

                if (!$gtin && trim($gtin) == '') {
                    $gtin = get_post_meta($productParentId, self::EAN_PRODUCT_KEY, true);
                }
            }

            if ($gtin) {
                return $gtin;
            }
        }

        return '';
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getPluginDataForSelect(): array
    {
        return [
            'active' => $this->isActive(),
            'name' => __('WooCommerce UPC, EAN, and ISBN', 'mergado-marketing-pack'),
            'hasFields' => $this->getPluginDataForSubselect()
        ];
    }

    public function getPluginDataForSubselect(): bool
    {
        return false;
    }
}
