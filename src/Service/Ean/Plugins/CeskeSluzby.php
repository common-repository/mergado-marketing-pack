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
 * Created (on plugin version): 0.5 and 0.6-alpha
 * Tested (on plugin version): 0.5 and 0.6-alpha
 */

namespace Mergado\Service\Ean\Plugins;

use Exception;

class CeskeSluzby
{
    private $isActive = false;
    private $globalData;

    public function __construct()
    {
        $this->prepareData();
    }

    /**
     * Prepare data for object instance
     */
    private function prepareData(): void
    {
        // ceske-sluzby-master = Fallback for github downloaders
        $normalCeskeSluzbyActive = is_plugin_active('ceske-sluzby/ceske-sluzby.php');
        $masterCeskeSluzbyActive = is_plugin_active('ceske-sluzby-master/ceske-sluzby.php');

        if ($normalCeskeSluzbyActive || $masterCeskeSluzbyActive) {
            $v = false;

            if ($normalCeskeSluzbyActive) {
                $folderName = '/ceske-sluzby/';
            } else {
                $folderName = '/ceske-sluzby-master/';
            }

            $pluginData = get_plugin_data(wp_normalize_path(WP_PLUGIN_DIR . $folderName . 'ceske-sluzby.php'));

            $v = $pluginData['Version'];

            //!!! If you read this, contact the support or use stable version. !!!
            //
            //This feature begins and was implemented in 0.6-alpha version of České Služby ... If someone use unstable version in future "like 0.7-alpha", we have to implement it.
            //I don't want to take the risk, that plugin dev make version alpha-0.7 and the php conversion makes something weird...

            if ($v === '0.6-alpha' || (is_numeric($v) && (float)$v > 0.5)) {
                try {
                    require_once wp_normalize_path(WP_PLUGIN_DIR . $folderName . 'includes/ceske-sluzby-functions.php');
                    require_once wp_normalize_path(WP_PLUGIN_DIR . $folderName . 'includes/class-ceske-sluzby-xml.php');

                    $this->globalData = ceske_sluzby_xml_ziskat_globalni_hodnoty();
                    $this->isActive = true;
                } catch (Exception $e) {
                    $this->isActive = false;
                }
            }
        }
    }

    /**
     * Return EAN from plugin
     *
     * @param $product
     * @param $productParentId
     * @param $selectedField
     * @return mixed|string
     */
    public function getEan($product, $productParentId, $selectedField)
    {
        if ($this->isActive()) {
            if (!$productParentId) {
                // Simple product
                $eanCode = ceske_sluzby_xml_ziskat_ean_produktu($this->globalData['podpora_ean'], $product['id'], $product['sku'], false, false);
            } else {
                // Variation product
                $eanCode = ceske_sluzby_xml_ziskat_ean_produktu($this->globalData['podpora_ean'], $product['id'], $product['sku'], $product['id'], $product['sku']);

                // Look for parent EAN
                if (!$eanCode || trim($eanCode) == '') {
                    $eanCode = ceske_sluzby_xml_ziskat_ean_produktu($this->globalData['podpora_ean'], $productParentId, false, false, false);
                }
            }

            return $eanCode;
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
            'name' => __('České služby pro WordPress', 'mergado-marketing-pack'),
            'hasFields' => $this->getPluginDataForSubselect()
        ];
    }

    public function getPluginDataForSubselect(): bool
    {
        return false;
    }
}
