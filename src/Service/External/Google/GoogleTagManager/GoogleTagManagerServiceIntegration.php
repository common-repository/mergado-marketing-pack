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

namespace Mergado\Service\External\Google\GoogleTagManager;

use Mergado;
use Mergado\Helper\AddToCartAjaxHelper;
use Mergado\Helper\ProductHelper;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class GoogleTagManagerServiceIntegration
{
    use SingletonTrait;

    private $googleTagManagerService;

    public function __construct()
    {
        $this->googleTagManagerService = GoogleTagManagerService::getInstance();
    }

    public function initDataLayer(): void
    {
        echo TemplateLoader::getTemplate(__DIR__ . '/templates/initDataLayer.php');
    }

    public function mainCodeHead(): void
    {
        $active = $this->googleTagManagerService->isActive();

        if ($active) {
            echo TemplateLoader::getTemplate(__DIR__ . '/templates/mainCodeHead.php', [
                'code' => $this->googleTagManagerService->getCode()
            ]);
        }
    }

    public function mainCodeAfterBody(): void
    {
        $active = $this->googleTagManagerService->isActive();

        if ($active) {
            echo TemplateLoader::getTemplate(__DIR__ . '/templates/mainCodeAfterBody.php', [
                'code' => $this->googleTagManagerService->getCode()
            ]);
        }
    }

    public function removeFromCartAjax(): void
    {
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if ($enhancedEcommerceActive) {
            global $woocommerce;
            $products = [];

            // Prepare data
            foreach ($woocommerce->cart->cart_contents as $key => $item) {
                $id = ProductHelper::getMergedIdFromCartItem($item);

                $category = get_the_terms($id, "product_cat");
                $categories = [];

                if ($category) {
                    foreach ($category as $term) {
                        $categories[] = $term->name;
                    }
                }

                $id = $item['product_id'];
                $name = $item['data']->get_name();
                $category = implode(', ', $categories);
                $price = $item['data']->get_price();

                $products[wc_get_cart_remove_url($key)] = [
                    'name' => $name,
                    'id' => $id,
                    'category' => $category,
                    'price' => (string)$price,
                ];
            }

            echo TemplateLoader::getTemplate(__DIR__ . '/templates/removeFromCartAjax.php', [
                'products' => $products,
                'currency' => get_woocommerce_currency(),
            ]);
        }
    }

    public function addToCartAjax(): void
    {
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if ($enhancedEcommerceActive) {
            echo TemplateLoader::getTemplate(__DIR__ . '/templates/addToCartAjax.php');
        }
    }

    public function addToCart() : string
    {
        $result = '';

        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], AddToCartAjaxHelper::getDisabledActionNames(), true)) {
            return $result;
        }

        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if ($enhancedEcommerceActive) {
            if (isset($_POST['add-to-cart'])) {
                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped') { // Check if grouped product
                    if (!isset($_POST['groupedGTM'])) { // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedGTM'] = true; // Set variable that disable next call of same addToCart

                        foreach ($_POST['quantity'] as $id => $quantity) {
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page

                            $categories = get_the_terms($id, 'product_cat');
                            $output = [];
                            if ($categories) {
                                foreach ($categories as $category) {
                                    $output[] = $category->name;
                                }
                            }

                            $productCategories = join(", ", $output);

                            $result .= TemplateLoader::getTemplate(__DIR__ . '/templates/addToCart.php', [
                                'currency' => get_woocommerce_currency(),
                                'name' => $product->get_name(),
                                'id' => $id,
                                'price' => $product->get_price(),
                                'quantity' => $quantity,
                                'category' => $productCategories
                            ]);
                        }
                    }
                } else {// Simple and complicated products
                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = join(", ", $output);

                    $result .= TemplateLoader::getTemplate(__DIR__ . '/templates/addToCart.php', [
                        'currency' => get_woocommerce_currency(),
                        'name' => $product->get_name(),
                        'id' => $id,
                        'price' => $product->get_price(),
                        'quantity' => $_POST['quantity'],
                        'category' => $productCategories
                    ]);
                }
            }
        }

        return $result;
    }

    public function purchase(): void
    {
        if (is_order_received_page()) {
            $orderId = empty($_GET['order']) ? ($GLOBALS['wp']->query_vars['order-received'] ? $GLOBALS['wp']->query_vars['order-received'] : 0) : absint($_GET['order']);
            $orderId_filter = apply_filters('woocommerce_thankyou_order_id', $orderId);

            if ($orderId_filter != '') {
                $orderId = $orderId_filter;
            }

            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if ($enhancedEcommerceActive) {
                $order = wc_get_order($orderId);
                $products_tmp = $order->get_items();

                $products = array();

                foreach ($products_tmp as $product) {
                    if ($product->get_variation_id() == 0) {
                        $id = $product->get_data()['product_id'];
                    } else {
                        $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = implode(", ", $output);

                    if ($this->googleTagManagerService->isConversionWithVat()) {
                        $productPrice = ($product->get_total() + $product->get_total_tax()) / $product->get_quantity();
                    } else {
                        $productPrice = $product->get_total() / $product->get_quantity();
                    }

                    $products[] = [
                        'name' => $product->get_name(),
                        'id' => $id,
                        'category' => $productCategories,
                        'quantity' => (int)$product->get_quantity(),
                        'price' => (string)$productPrice,
                    ];

                }

                global $woocommerce;
                $coupons = implode(', ', $woocommerce->cart->get_applied_coupons());

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/purchase.php', [
                    'currencyCode' => $order->get_currency(),
                    'id' => $order->get_id(),
                    'affiliation' => get_bloginfo('name'),
                    'revenue' => $order->get_total(),
                    'tax' => $order->get_total_tax(),
                    'shipping' => $order->get_shipping_total(),
                    'coupon' => $coupons,
                    'products' => $products
                ]);
            }
        }
    }

    public function transaction(): void
    {
        if (is_order_received_page()) {
            $orderId = empty($_GET['order']) ? ($GLOBALS['wp']->query_vars['order-received'] ? $GLOBALS['wp']->query_vars['order-received'] : 0) : absint($_GET['order']);
            $orderId_filter = apply_filters('woocommerce_thankyou_order_id', $orderId);

            if ($orderId_filter != '') {
                $orderId = $orderId_filter;
            }

            $ecommerceActive = $this->googleTagManagerService->isEcommerceActive();
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if ($ecommerceActive && !$enhancedEcommerceActive) {
                $order = wc_get_order($orderId);
                $products_tmp = $order->get_items();

                $products = array();

                foreach ($products_tmp as $product) {
                    if ($product->get_variation_id() == 0) {
                        $id = $product->get_data()['product_id'];
                    } else {
                        $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    }

                    $categories = get_the_terms($id, 'product_cat');

                    $output = [];
                    if ($categories) {
                        foreach ($categories as $category) {
                            $output[] = $category->name;
                        }
                    }

                    $productCategories = implode(", ", $output);

                    if ($this->googleTagManagerService->isConversionWithVat()) {
                        $productPrice = ($product->get_total() + $product->get_total_tax()) / $product->get_quantity();
                    } else {
                        $productPrice = $product->get_total() / $product->get_quantity();
                    }

                    $products[] = [
                        'name' => $product->get_name(),
                        'sku' => (string)$id,
                        'category' => $productCategories,
                        'quantity' => (int)$product->get_quantity(),
                        'price' => (string)$productPrice,
                    ];

                }

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/transaction.php', [
                    'transactionId' => $order->get_id(),
                    'transactionAffiliation' => get_bloginfo('name'),
                    'transactionTotal' => $order->get_total(),
                    'transactionTax' => $order->get_total_tax(),
                    'transactionShipping' => $order->get_shipping_total(),
                    'transactionProducts' => json_encode($products, JSON_NUMERIC_CHECK)
                ]);
            }
        }
    }

    public function productDetailView(): void
    {
        if (is_product()) {
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if ($enhancedEcommerceActive) {
                $id = get_queried_object_id();
                $product = wc_get_product($id);

                $categories = get_the_terms($id, 'product_cat');

                $output = [];
                if ($categories) {
                    foreach ($categories as $category) {
                        $output[] = $category->name;
                    }
                }

                $productCategories = implode(", ", $output);

                $productData[] = [
                    'name' => $product->get_name(),
                    'id' => (string)$id,
                    'price' => $product->get_price(),
                    'category' => $productCategories,
                ];

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/productDetailView.php', [
                    'currency' => get_woocommerce_currency(),
                    'products' => json_encode($productData, JSON_NUMERIC_CHECK)
                ]);

                //If user come from my url === clicked on product url
                $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
                if (!$pageWasRefreshed) {
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER["HTTP_REFERER"], get_site_url()) !== false) {

                        echo TemplateLoader::getTemplate(__DIR__ . '/templates/productClick.php', [
                            'currency' => get_woocommerce_currency(),
                            'products' => json_encode($productData, JSON_NUMERIC_CHECK)
                        ]);
                    }
                }
            }
        }
    }

    public function checkoutManipulation(): void
    {
        $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

        if ($enhancedEcommerceActive) {
            echo TemplateLoader::getTemplate(__DIR__ . '/templates/checkoutManipulation.php');
        }
    }

    public function checkoutStep(): void
    {
        if (is_cart() || is_checkout()) {
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if ($enhancedEcommerceActive) {
                global $woocommerce;

                $products = [];

                foreach ($woocommerce->cart->cart_contents as $key => $item) {
                    $id = ProductHelper::getMergedIdFromCartItem($item);

                    $category = get_the_terms($id, "product_cat");
                    $categories = [];

                    if ($category) {
                        foreach ($category as $term) {
                            $categories[] = $term->name;
                        }
                    }

                    $name = $item['data']->get_name();
                    $category = implode(', ', $categories);
                    $price = $item['data']->get_price();

                    $products[] = [
                        'id' => $id,
                        'name' => $name,
                        'category' => $category,
                        'price' => (string)$price,
                    ];
                };

                if (is_cart()) {
                    $step = 1;
                } else if (is_checkout()) {
                    $step = 2;
                } else {
                    $step = 0;
                }

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/checkoutStep.php', [
                    'currency' => get_woocommerce_currency(),
                    'step' => $step,
                    'products' => json_encode($products, JSON_NUMERIC_CHECK)
                ]);
            }
        }
    }

    public function viewList(): void
    {
        if (is_shop() || is_product_category() || is_search()) {
            $enhancedEcommerceActive = $this->googleTagManagerService->isEnhancedEcommerceActive();

            if ($enhancedEcommerceActive) {
                if (is_shop()) {
                    $list_name = 'shop';
                } else if (is_product_category()) {
                    $list_name = get_queried_object()->name;
                } else if (is_search()) {
                    $list_name = 'search';
                } else {
                    $list_name = '';
                }

                $viewListItemsCount = $this->googleTagManagerService->getViewListItemsCount();

                echo TemplateLoader::getTemplate(__DIR__ . '/templates/viewList.php', [
                    'currency' => get_woocommerce_currency(),
                    'listName' => $list_name,
                    'viewListItemsCount' => (int)$viewListItemsCount
                ]);
            }
        }
    }
}
