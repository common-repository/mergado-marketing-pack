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
 * Created (on plugin version): 2.1.1
 * Tested (on plugin version): 2.1.1
 */

namespace Mergado\Service\Ean\Plugins;

use Alg_WC_EAN_Core;

class WpssoWcMetadata
{
    public const PLUGIN_PATH = 'wpsso-wc-metadata/wpsso-wc-metadata.php';

    public const KEYS_EAN = [
        '_wpsso_product_mfr_part_no' => ['name' => 'MPN'],
        '_wpsso_product_isbn' => ['name' => 'ISBN'],
        '_wpsso_product_gtin14' => ['name' => 'GTIN-14'],
        '_wpsso_product_gtin13' => ['name' => 'GTIN13 (EAN)'],
        '_wpsso_product_gtin12' => ['name' => 'GTIN-12 (UPC)'],
        '_wpsso_product_gtin8' => ['name' => 'GTIN-8'],
        '_wpsso_product_gtin' => ['name' => 'GTIN'],
    ];

    private $isActive = false;

    public function __construct()
    {
        $this->prepareData();
    }

    private function prepareData(): void
    {
        $this->isActive = is_plugin_active(self::PLUGIN_PATH);
    }

    public function getEan($productId, $productParentId, $selectedField)
    {
        if ($this->isActive()) {
            $eanCode = get_post_meta($productId, $selectedField, true);

            if (!$eanCode || trim($eanCode) === '') {
                $eanCode = get_post_meta($productParentId, $selectedField, true);
            }

            if ($eanCode) {
                return $eanCode;
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
            'name' => __('WPSSO Product Metadata (GTIN, UPC, EAN, ISBN, MPN, Global Identifier) for WooCommerce', 'mergado-marketing-pack'),
            'hasFields' => true
        ];
    }

    public function getPluginDataForSubselect(): array
    {
        return self::KEYS_EAN;
    }
}
