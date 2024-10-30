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

namespace Mergado\Service\External\Facebook;

use Mergado\Helper\AddToCartAjaxHelper;
use Mergado\Helper\ProductHelper;
use Mergado\Service\CookieService;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class FacebookServiceIntegration
{
    use SingletonTrait;

    /**
     * @var FacebookService
     */
    private $facebookService;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->facebookService = FacebookService::getInstance();
        $this->cookieService = CookieService::getInstance();
    }

    public function init(): void
    {
        if ($this->facebookService->isActive()) {

            /**
             * Main code
             */

            if ($this->cookieService->advertisementEnabled()) {
                $consent = 'grant';
            } else {
                $consent = 'revoke';
            }

            echo TemplateLoader::getTemplate(__DIR__ . '/templates/main.php', [
                'consent' => $consent,
                'code' => $this->facebookService->getCode()
            ]);


            /**
             * Product page
             */

            if (is_product()) {
                $product = get_queried_object();

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/viewContent.php', [
                    'productTitle' => $product->post_title,
                    'productId' => $product->ID
                ]);
            }


            /**
             * Product category
             */

            if (is_product_category()) {
                $category = get_queried_object();
                $products_tmp = wc_get_products(['category' => [$category->slug]]);
                $products = [];

                foreach ($products_tmp as $product) {
                    $id = $product->get_id();

                    $products['ids'][] = "'" . $id . "'";
                }

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/viewCategory.php', [
                    'categoryName' => $category->name,
                    'contentType' => 'product',
                    'productIds' => $products['ids'] ?? []
                ]);
            }


            /**
             * Search
             */

            if (is_search()) {
                $searchQuery = get_search_query();
                $products = ['ids' => []];

                global $wp_query;

                $posts = $wp_query->get_posts();

                foreach ($posts as $post) {
                    if (get_post_type($post) === 'product') {
                        $product = wc_get_product($post->ID);

                        $products['ids'][] = "'" . $product->get_id() . "'";
                    }
                }

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/search.php', [
                    'searchQuery' => $searchQuery,
                    'contentType' => 'product',
                    'productIds' => $products['ids'] ?? []
                ]);
            }
        }
    }

    public function initiateCheckout(): void
    {
        if ($this->facebookService->isActive()) {
            global $woocommerce;

            $products = [];
            $quantity = 0;

            foreach ($woocommerce->cart->cart_contents as $item) {
                $id = ProductHelper::getMergedIdFromCartItem($item);

                $products['ids'][] = "'" . $id . "'";
                $products['contents'][] = "{'id':'" . $id . "', 'quantity':'" . $item['quantity'] . "'}";
                $quantity = $quantity + $item['quantity'];
            }

            if ($this->facebookService->isConversionWithVat()) {
                $conversionValue = number_format((float)$woocommerce->cart->get_cart_contents_total(), wc_get_price_decimals(), '.', '');
            } else {
                $conversionValue = number_format((float)$woocommerce->cart->get_cart_contents_total() - $woocommerce->cart->get_cart_contents_tax(), wc_get_price_decimals(), '.', '');
            }

            $templatePath = __DIR__ . '/templates/initiateCheckout.php';

            $templateVariables = [
                'contentIds' => $products['ids'],
                'contents' => $products['contents'],
                'contentType' => 'product',
                'value' => $conversionValue,
                'currency' => get_woocommerce_currency(),
                'numItems' => $quantity,
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function purchased($orderId): void
    {
        if ($this->facebookService->isActive()) {
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();

            $products = [];

            foreach ($products_tmp as $product) {
                if ($product->get_variation_id() == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                }
                $products['ids'][] = "'" . $id . "'";
                $products['contents'][] = "{'id':'" . $id . "', 'quantity':'" . $product['quantity'] . "'}";
            }

            if ($this->facebookService->isConversionWithVat()) {
                $conversionValue = number_format((float)$order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
            } else {
                $conversionValue = number_format((float)$order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '');
            }

            $templatePath = __DIR__ . '/templates/purchase.php';

            $templateVariables = [
                'contentIds' => $products['ids'],
                'contents' => $products['contents'],
                'contentType' => 'product',
                'value' => $conversionValue,
                'currency' => get_woocommerce_currency()
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function addToCart() : string
    {
        $result = '';

        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], AddToCartAjaxHelper::getDisabledActionNames(), true)) {
            return $result;
        }

        $currency = get_woocommerce_currency();

        if ($this->facebookService->isActive()) {
            if (isset($_POST['add-to-cart'])) {
                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped') { // Check if grouped product
                    if (!isset($_POST['groupedFbPixel'])) { // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedFbPixel'] = true; // Set variable that disable next call of same addToCart
                        foreach ($_POST['quantity'] as $id => $quantity) {
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page

                            $result .= TemplateLoader::getTemplate(__DIR__ . '/templates/addToCart.php', [
                                'productName' => $product->get_name(),
                                'contentIds' => $id,
                                'id' => $id,
                                'quantity' => $quantity,
                                'contentType' => 'product',
                                'value' => $product->get_price(),
                                'currency' => $currency
                            ]);
                        }
                    }
                } else {
                    // Merged Id
                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    // Default quantity 1
                    if (isset($_POST['quantity'])) {
                        $quantity = (int)$_POST['quantity'];
                    } else {
                        $quantity = 1;
                    }

                    $result .= TemplateLoader::getTemplate(__DIR__ . '/templates/addToCart.php', [
                        'productName' => $product->get_name(),
                        'contentIds' => $id,
                        'id' => $id,
                        'quantity' => $quantity,
                        'contentType' => 'product',
                        'value' => $product->get_price(),
                        'currency' => $currency
                    ]);
                }
            }
        }

        return $result;
    }

    public function addToCartAjax(): void
    {
        if ($this->facebookService->isActive()) {
            echo TemplateLoader::getTemplate(__DIR__ . '/templates/addToCartAjax.php', []);
        }
    }
}
