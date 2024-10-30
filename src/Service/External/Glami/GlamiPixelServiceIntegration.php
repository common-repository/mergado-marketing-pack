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

namespace Mergado\Service\External\Glami;

use Mergado\Helper\AddToCartAjaxHelper;
use Mergado\Helper\LanguageHelper;
use Mergado\Service\CookieService;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class GlamiPixelServiceIntegration
{
    use SingletonTrait;

    private $glamiPixelService;
    private $lang;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->lang = LanguageHelper::getLang();
        $this->glamiPixelService = GlamiPixelService::getInstance();
        $this->cookieService = CookieService::getInstance();
    }

    public function init()
    {
        if ($this->glamiPixelService->isActive($this->lang)) {
            $templatePath = __DIR__ . '/templates/pixel/init.php';

            $templateVariables = [
                'glamiCode' => $this->glamiPixelService->getCode($this->lang),
                'lang' => strtolower($this->lang),
                'consent' => (int)$this->cookieService->advertisementEnabled()
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function viewContent()
    {
        if ($this->glamiPixelService->isActive($this->lang)) {

            /**
             * Product category
             * - old comments says, that there is no way to get variation ID, so we send only the default one
             */
            if (is_product_category()) {
                $category = get_queried_object();
                $products_tmp = wc_get_products(array('category' => array($category->slug)));
                $products = [];

                if (count($products_tmp) > 0) {
                    foreach ($products_tmp as $product) {
                        $id = $product->get_id();

                        $products['ids'][] = "'" . $id . "'";
                        $products['name'][] = "'" . $product->get_name() . "'";
                    }

                    $products['ids'] = implode(',', $products['ids']);
                    $products['name'] = implode(',', $products['name']);
                }

                $templatePath = __DIR__ . '/templates/pixel/viewContentCategory.php';

                $templateVariables = [
                    'contentType' => 'category',
                    'itemIds' => $products['ids'] ?? '',
                    'productNames' => $products['name'] ?? '',
                    'categoryId' => $category->term_id,
                    'categoryText' => $category->name,
                    'consent' => (int)$this->cookieService->advertisementEnabled()
                ];

                echo TemplateLoader::getTemplate($templatePath, $templateVariables);
            }


            /**
             * Product detail
             * - old comments says, that there is no way to get variation ID, so we send only the default one
             * - OLD CODE: $id = $product->get_id() . '-' . $product->get_variation_id();
             */
            if (is_product()) {
                $product = get_queried_object();

                $id = $product->ID;

                $templatePath = __DIR__ . '/templates/pixel/viewContentProduct.php';

                $templateVariables = [
                    'contentType' => 'product',
                    'itemIds' => $id,
                    'productNames' => $product->post_title,
                    'consent' => (int)$this->cookieService->advertisementEnabled()
                ];

                echo TemplateLoader::getTemplate($templatePath, $templateVariables);
            }
        }
    }

    public function purchased($orderId)
    {
        if ($this->glamiPixelService->isActive($this->lang)) {
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();
            $products = array();

            foreach ($products_tmp as $product) {
                if ($product->get_data()['variation_id'] == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_data()['variation_id'];
                }

                $products['ids'][] = "'" . $id . "'";
                $products['name'][] = "'" . $product->get_name() . "'";
            }

            if ($this->glamiPixelService->isConversionWithVat()) {
                $conversionValue = number_format((float)$order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
            } else {
                $conversionValue = number_format((float)$order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '');
            }

            $templatePath = __DIR__ . '/templates/pixel/purchase.php';

            $templateVariables = [
                'itemIds' => $products['ids'],
                'productNames' => $products['name'],
                'value' => $conversionValue,
                'currency' => get_woocommerce_currency(),
                'transactionId' => $orderId,
                'consent' => (int)$this->cookieService->advertisementEnabled()
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function addToCart() : string
    {
        $result = '';

        // Disable if woodmart theme is active (compatibility issue)
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], AddToCartAjaxHelper::getDisabledActionNames(), true)) {
            return $result;
        }

        if ($this->glamiPixelService->isActive($this->lang)) {
            if (isset($_POST['add-to-cart'])) {

                $product = wc_get_product($_POST['add-to-cart']);

                /**
                 * Grouped products
                 */
                if ($product->get_type() === 'grouped') {
                    if (!isset($_POST['groupedGlami'])) { // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedGlami'] = true; // Set variable that disable next call of same addToCart

                        foreach ($_POST['quantity'] as $id => $quantity) {
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page

                            $templateVariables = [
                                'itemIds' => $id,
                                'productNames' => $product->get_name(),
                                'value' => $product->get_price(),
                                'currency' => get_woocommerce_currency(),
                                'consent' => (int)$this->cookieService->advertisementEnabled()
                            ];

                            $result .= TemplateLoader::getTemplate(__DIR__ . '/templates/pixel/addToCart.php', $templateVariables);
                        }
                    }

                /**
                 * Simple/variable products
                 */
                } else {
                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $templateVariables = [
                        'itemIds' => $id,
                        'productNames' => $product->get_name(),
                        'value' => $product->get_price(),
                        'currency' => get_woocommerce_currency(),
                        'consent' => (int)$this->cookieService->advertisementEnabled()
                    ];

                    $result .= TemplateLoader::getTemplate(__DIR__ . '/templates/pixel/addToCart.php', $templateVariables);
                }
            }
        }

        return $result;
    }

    public function addToCartAjax()
    {
        if ($this->glamiPixelService->isActive($this->lang)) {
            $templatePath = __DIR__ . '/templates/pixel/addToCartAjax.php';

            $templateVariables = [
                'consent' => (int)$this->cookieService->advertisementEnabled()
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }
}
