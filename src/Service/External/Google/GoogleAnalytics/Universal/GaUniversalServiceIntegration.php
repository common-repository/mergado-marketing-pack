<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\Universal;

use Mergado\Service\External\Google\Gtag\GtagIntegrationHelper;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class GaUniversalServiceIntegration
{
    use SingletonTrait;

    /**
     * @var GaUniversalService
     */
    private $googleUniversalAnalyticsService;
    /**
     * @var false|string|null
     */
    private $sendTo;

    public function __construct()
    {
        $this->googleUniversalAnalyticsService = GaUniversalService::getInstance();
        $this->sendTo = $this->getFormattedAnalyticsCode();
    }

    public function getFormattedAnalyticsCode(): string
    {
        $gaMeasurementId = $this->googleUniversalAnalyticsService->getCode();

        // add prefix if not exist
        if (trim($gaMeasurementId) !== '' && strpos($gaMeasurementId, "UA-") !== 0) {
            $gaMeasurementId = 'UA-' . $gaMeasurementId;
        }

        return $gaMeasurementId;
    }

    public function removeFromCart(): void
    {
        if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
            global $woocommerce;
            $products = [];

            foreach ($woocommerce->cart->cart_contents as $key => $item) {
                if ($item['variation_id'] == 0) {
                    $id = $item['product_id'];
                } else {
                    $id = $item['product_id'] . '-' . $item['variation_id'];
                }

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
                    'id' => $id,
                    'name' => $name,
                    'category' => $category,
                    'price' => (string)$price,
                ];
            }

            $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/removeFromCart.php';

            $templateVariables = [
                'currency' => get_woocommerce_currency(),
                'products' => htmlspecialchars(json_encode($products, JSON_NUMERIC_CHECK), ENT_QUOTES),
                'sendTo' => $this->sendTo
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function purchased($orderId): void
    {
        if ($this->googleUniversalAnalyticsService->isActiveEcommerce()) {
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

                if ($this->googleUniversalAnalyticsService->isConversionWithVat()) {
                    $productPrice = ($product->get_total() + $product->get_total_tax()) / $product->get_quantity();
                } else {
                    $productPrice = $product->get_total() / $product->get_quantity();
                }

                $products[] = [
                    'id' => $id,
                    'name' => $product->get_name(),
                    'category' => $productCategories,
                    'quantity' => (int)$product->get_quantity(),
                    'price' => (string)$productPrice,
                ];
            }

            $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/purchased.php';

            $templateVariables = [
                'transactionId' => $order->get_id(),
                'affiliation' => get_bloginfo('name'),
                'value' => $order->get_total(),
                'currency' => $order->get_currency(),
                'tax' => $order->get_total_tax(),
                'shipping' => $order->get_shipping_total(),
                'items' => json_encode($products, JSON_NUMERIC_CHECK),
                'googleBusinessVertical' => 'retail',
                'sendTo' => $this->sendTo
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function checkoutStep(): void
    {
        if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
            global $woocommerce;

            $products = [];

            foreach ($woocommerce->cart->cart_contents as $key => $item) {
                if ($item['variation_id'] == 0) {
                    $id = $item['product_id'];
                } else {
                    $id = $item['product_id'] . '-' . $item['variation_id'];
                }

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
            }

            $coupons = implode(', ', $woocommerce->cart->get_applied_coupons());

            $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/checkoutStep1.php';

            $templateVariables = [
                'currency' => get_woocommerce_currency(),
                'checkoutStep' => 1,
                'items' => json_encode($products, JSON_NUMERIC_CHECK),
                'coupon' => $coupons,
                'sendTo' => $this->sendTo
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function checkoutManipulation(): void
    {
        if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
            $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/checkoutManipulation.php';

            $templateVariables = [
                'sendTo' => $this->sendTo
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function viewItemList(): void
    {
        if (is_shop() || is_product_category() || is_search()) {
            if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
                GtagIntegrationHelper::viewItemList($this->sendTo);
            }
        }
    }

    public function productDetailView(): void
    {
        if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
            GtagIntegrationHelper::productDetailView($this->sendTo);
        }
    }

    public function addToCart() : string
    {
        if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
            return GtagIntegrationHelper::addToCart($this->sendTo);
        }

        return '';
    }

    public function addToCartAjax(): void
    {
        if ($this->googleUniversalAnalyticsService->isActiveEnhancedEcommerce()) {
            GtagIntegrationHelper::addToCartAjax($this->sendTo);
        }
    }
}
