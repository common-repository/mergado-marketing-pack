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

class EanForWoocommerce
{
    public const PLUGIN_PATH = 'ean-for-woocommerce/ean-for-woocommerce.php';

    private $isActive = false;

    public function __construct()
    {
        $this->prepareData();

        if ($this->isActive()) {
            require_once wp_normalize_path(WP_PLUGIN_DIR . '/' . 'ean-for-woocommerce/includes/class-alg-wc-ean-core.php');
        }
    }

    private function prepareData()
    {
        $this->isActive = is_plugin_active(self::PLUGIN_PATH);
    }

    public function getEan($productId, $parentProductId, $selectedField)
    {
        if ($this->isActive()) {
            $classInstance = new Alg_WC_EAN_Core();
            $eanCode = $classInstance->get_ean($productId, true);

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
            'name' => __('EAN for WooCommerce', 'mergado-marketing-pack'),
            'hasFields' => $this->getPluginDataForSubselect()
        ];
    }

    public function getPluginDataForSubselect(): bool
    {
        return false;
    }
}
