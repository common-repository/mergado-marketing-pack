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

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4;

use Exception;
use Mergado;
use Mergado\Helper\AddToCartAjaxHelper;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4ItemEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4ItemsEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4AddToCartEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4PurchaseEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4RefundEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4RemoveFromCartEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4SearchEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4SelectContentEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4SelectItemEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4ViewCartEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4ViewItemEventObject;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\Ga4ViewItemListEventObject;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;
use WC_Coupon;
use WC_Meta_Data;
use WC_Order_Item_Coupon;
use WC_Order_Item_Product;
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variable;
use WooCommerce;

class Ga4ServiceIntegration
{
    use SingletonTrait;

    /**
     * @var Ga4Service
     */
    private $ga4Service;
    /**
     * @var false|string|null
     */
    private $sendTo;

    public function __construct()
    {
        $this->ga4Service = Ga4Service::getInstance();
        $this->sendTo = $this->getFormattedAnalyticsCode();
    }

    public function getFormattedAnalyticsCode(): string
    {
        $gaMeasurementId = $this->ga4Service->getCode();

        // add prefix if not exist
        if (trim($gaMeasurementId) !== '' && strpos($gaMeasurementId, "G-") !== 0) {
            $gaMeasurementId = 'G-' . $gaMeasurementId;
        }

        return $gaMeasurementId;
    }

    public function viewCart(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            $cartDataObject = $this->getCartDataObject();

            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/viewCart.php',
                ['eventObject' => $cartDataObject]
            );
        }
    }

    /**
     * @throws Exception
     */
    public function getCartDataObject(): array
    {
        $eventItemsObject = $this->getCartEventItemsObject();

        $eventObject = new Ga4ViewCartEventObject();
        $eventObject
            ->setValue((float)$this->getCartValue())
            ->setCurrency(get_woocommerce_currency())
            ->setItems($eventItemsObject)
            ->setSendTo($this->sendTo);

        return $eventObject->getResult();
    }

    public function getCartGlobalCoupon(): string
    {
        return implode(', ', WC()->cart->get_applied_coupons());
    }

    public function getCartEventItemsObject(bool $removeUrlAsKey = false): BaseGa4ItemsEventObject
    {
        /**
         * @var $woocommerce WooCommerce
         */
        global $woocommerce;

        $eventItemsObject = new BaseGa4ItemsEventObject();


        foreach ($woocommerce->cart->cart_contents as $key => $item) {
            $id = $this->getProductId($item);

            if ($item['variation_id']) {
                $appliedCoupons = $this->getCartProductAppliedCoupons($item['product_id'], $item['variation_id']);
            } else {
                $appliedCoupons = $this->getCartProductAppliedCoupons($item['product_id']);
            }

            /**
             * @var $product WC_Product_Simple
             */
            $product = $item['data'];

            $prices = $this->getCartProductPrices($product, $item);

            $eventItemObject = new BaseGa4ItemEventObject();
            $eventItemObject
                ->setItemId($id)
                ->setItemName($product->get_name())
                ->setItemCategories($this->getProductCategories($id))
                ->setQuantity($item['quantity'])
                ->setCoupon($appliedCoupons)
                ->setPrice((float)$prices['price'])
                ->setDiscount((float)$prices['discount']);

            if ($item['variation_id']) {
                $eventItemObject->setItemVariant($item['variation_id']);
            }

            if ($removeUrlAsKey) {
                $eventItemsObject->addItem($eventItemObject, wc_get_cart_remove_url($key));
            } else {
                $eventItemsObject->addItem($eventItemObject);
            }
        }

        return $eventItemsObject;
    }

    public function getProductId($item)
    {
        if ($item['variation_id'] == 0) {
            $id = $item['product_id'];
        } else {
            $id = $item['product_id'] . '-' . $item['variation_id'];
        }

        return $id;
    }

    /**
     * Prices and taxes inside $items are not affected by woocommerce tax settings
     */
    public function getCartProductPrices(WC_Product $product, $item): array
    {
        $cartItemTotal = $item['line_total'];
        $cartItemTotalTax = $item['line_tax'];
        $cartItemQuantity = $item['quantity'];

        if ($this->ga4Service->isConversionWithVat()) {
            $regularPrice = (float)wc_get_price_including_tax($product, ['price' => $product->get_regular_price()]); // Normal product price without discount
            $price = ($cartItemTotal + $cartItemTotalTax) / $cartItemQuantity;
        } else {
            $regularPrice = (float)wc_get_price_excluding_tax($product, ['price' => $product->get_regular_price()]); // Normal product price without discount
            $price = $cartItemTotal / $cartItemQuantity;
        }

        return ['price' => $price, 'regularPrice' => $regularPrice, 'discount' => $this->getDiscount($price, $regularPrice)];
    }

    public function getProductPrices(WC_Product $product): array
    {
        if ($this->ga4Service->isConversionWithVat()) {
            $regularPrice = (float)wc_get_price_including_tax($product, ['price' => $product->get_regular_price()]); // Normal product price without discount
            $price = (float)wc_get_price_including_tax($product); // Price after product discount
        } else {
            $regularPrice = (float)wc_get_price_excluding_tax($product, ['price' => $product->get_regular_price()]); // Normal product price without discount
            $price = (float)wc_get_price_excluding_tax($product); // Price after product discount
        }

        return ['price' => $price, 'regularPrice' => $regularPrice, 'discount' => $this->getDiscount($price, $regularPrice)];
    }

    /**
     * Prices and taxes inside $orderItem are not affected by woocomerce tax settings
     */
    public function getPurchaseProductPrices(WC_Order_Item_Product $orderItem, WC_Product $product): array
    {
        $orderItemTotal = (float)$orderItem->get_total();
        $orderItemTotalTax = (float)$orderItem->get_total_tax();
        $orderItemQuantity = $orderItem->get_quantity();

        if ($this->ga4Service->isConversionWithVat()) {
            $regularPrice = (float)wc_get_price_including_tax($product, ['price' => $product->get_regular_price()]); // Normal product price without discount
            $price = ($orderItemTotal + $orderItemTotalTax) / $orderItemQuantity;
        } else {
            $regularPrice = (float)wc_get_price_excluding_tax($product, ['price' => $product->get_regular_price()]); // Normal product price without discount
            $price = $orderItemTotal / $orderItemQuantity;
        }

        return ['price' => $price, 'regularPrice' => $regularPrice, 'discount' => $this->getDiscount($price, $regularPrice)];
    }

    public function getDiscount($price, $regularPrice): float
    {
        return round($price !== $regularPrice ? $regularPrice - $price : 0, 2);
    }

    public function getProductCategories($id): array
    {
        $categories = get_the_terms($id, 'product_cat');

        $productCategories = [];
        if ($categories) {
            foreach ($categories as $category) {
                $productCategories[] = $category->name;
            }
        }

        return $productCategories;
    }

    public function getCartValue(): float
    {
        if ($this->ga4Service->isConversionWithVat()) {
            $cartValue = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() + WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax();

            if (!$this->ga4Service->getShippingPriceIncluded()) {
                $cartValue = $cartValue - (WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax());
            }
        } else {
            $cartValue = WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total();

            if (!$this->ga4Service->getShippingPriceIncluded()) {
                $cartValue = $cartValue - WC()->cart->get_shipping_total();
            }
        }

        return $cartValue;
    }

    public function addPaymentInfo(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/addPaymentInfo.php', ['payment_methods' => $this->getPaymentMethods()]
            );
        }
    }

    public function addShippingInfo(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            $shippingMethods = $this->getShippingMethods();

            if ($shippingMethods) {
                echo TemplateLoader::getTemplate(
                    __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/addShippingInfo.php', ['shipping_methods' => $shippingMethods]
                );
            }
        }
    }

    public function addToCart() : string
    {
        $result = '';

        if ($this->ga4Service->isActiveEcommerce()) {

            // Disable if woodmart theme because of incompatibility
            if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], AddToCartAjaxHelper::getDisabledActionNames(), true)) {
                return $result;
            }

            if (isset($_POST['add-to-cart'])) {
                $product = wc_get_product($_POST['add-to-cart']);

                $eventItemsObject = new BaseGa4ItemsEventObject();

                if ($product->get_type() === 'grouped') { // Check if grouped product
                    if (!isset($_POST['groupedGTAG4'])) { // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedGTAG4'] = true; // Set variable that disable next call of same addToCart

                        $totalMonetaryPrice = 0;

                        foreach ($_POST['quantity'] as $id => $quantity) {
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page

                            $eventItemObject = new BaseGa4ItemEventObject();

                            $prices = $this->getProductPrices($product);
                            $totalMonetaryPrice = $totalMonetaryPrice + ($prices['price'] * $_POST['quantity']);

                            $eventItemObject
                                ->setItemId($id)
                                ->setItemName($product->get_name())
                                ->setItemCategories($this->getProductCategories($id))
                                ->setQuantity($_POST['quantity'])
                                ->setPrice((float)$prices['price'])
                                ->setDiscount((float)$prices['discount']);

                            // No variant ID

                            $eventItemsObject->addItem($eventItemObject);
                        }

                        $eventObject = new Ga4AddToCartEventObject();
                        $eventObject
                            ->setValue($totalMonetaryPrice)
                            ->setCurrency(get_woocommerce_currency())
                            ->setItems($eventItemsObject)
                            ->setSendTo($this->sendTo);

                        $result .= TemplateLoader::getTemplate(__MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/addToCart.php',
                            ['eventObject' => $eventObject->getResult()]);
                    }
                } else {
                    $product = wc_get_product($_POST['add-to-cart']);

                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $eventItemObject = new BaseGa4ItemEventObject();

                    $prices = $this->getProductPrices($product);

                    $eventItemObject
                        ->setItemId($id)
                        ->setItemName($product->get_name())
                        ->setItemCategories($this->getProductCategories($id))
                        ->setQuantity($_POST['quantity'])
                        ->setPrice((float)$prices['price'])
                        ->setDiscount((float)$prices['discount']);

                    if (isset($_POST['variation_id']) && $_POST['variation_id']) {
                        $eventItemObject->setItemVariant($_POST['variation_id']);
                    }

                    $eventItemsObject->addItem($eventItemObject);

                    $eventObject = new Ga4AddToCartEventObject();
                    $eventObject
                        ->setValue((float)$prices['price'] * $_POST['quantity'])
                        ->setCurrency(get_woocommerce_currency())
                        ->setItems($eventItemsObject)
                        ->setSendTo($this->sendTo);

                    $result .= TemplateLoader::getTemplate(
                        __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/addToCart.php',
                        ['eventObject' => $eventObject->getResult()]
                    );
                }
            }
        }

        return $result;
    }

    public function addToCartAjax(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            $eventObject = new Ga4AddToCartEventObject();
            $eventObject
                ->setCurrency(get_woocommerce_currency())
                ->setSendTo($this->sendTo);

            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/addToCartAjax.php',
                ['eventObject' => $eventObject->getResult(true), 'withVat' => $this->ga4Service->isConversionWithVat()]
            );
        }
    }

    public function search(): void
    {
        if ($this->ga4Service->isActiveEcommerce() && is_search()) {
            $eventObject = new Ga4SearchEventObject(get_search_query());

            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/search.php',
                ['eventObject' => $eventObject->getResult()]
            );
        }
    }

    public function beginCheckout(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/beginCheckout.php'
            );
        }
    }

    public function productDetailView(): void
    {
        if ($this->ga4Service->isActiveEcommerce() && is_product()) {
            /**
             * @var $product WC_Product_Variable | WC_product
             */
            global $product;

            $id = get_queried_object_id();

            if ($product->is_type('variable')) {
                $variations = $product->get_available_variations();

                if (count($_GET) > 0 && $variations && is_array($variations) && count($variations) > 0) {
                    foreach ($variations as $variation) {
                        $isVariant = true;

                        foreach ($variation['attributes'] as $attrName => $attrValue) {
                            // If attribute not in get and attribute value is "" || attribute is set in get and value is same
                            if (isset($_GET[$attrName])) {
                                if ($_GET[$attrName] !== $attrValue) {
                                    $isVariant = false;
                                    break;
                                }
                            } else {
                                if ($attrValue !== '') {
                                    $isVariant = false;
                                    break;
                                }
                            }
                        }

                        if ($isVariant) {
                            $variableId = $variation['variation_id'];
                            $id = $id . '-' . $variableId;
                            break;
                        }
                    }
                }
            }

            $itemPrices = $this->getProductPrices($product);

            $eventItemsObject = new BaseGa4ItemsEventObject();
            $eventItemObject = new BaseGa4ItemEventObject();
            $eventItemObject
                ->setItemId($id)
                ->setItemName($product->get_name())
                ->setItemCategories($this->getProductCategories($id))
                ->setPrice($itemPrices['price'])
                ->setDiscount($itemPrices['discount']);

            if (isset($variableId) && $variableId) {
                $eventItemObject->setItemVariant($variableId);
            }

            $eventItemsObject->addItem($eventItemObject);

            $eventObject = new Ga4ViewItemEventObject();
            $eventObject
                ->setValue($itemPrices['price'])
                ->setCurrency(get_woocommerce_currency())
                ->setItems($eventItemsObject)
                ->setSendTo($this->sendTo);

            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/viewItem.php',
                ['eventObject' => $eventObject->getResult()]
            );

            //If user come from my url === clicked on product url
            if (isset($_SERVER["HTTP_REFERER"])) {
                if (strpos($_SERVER["HTTP_REFERER"], get_site_url()) !== false) {

                    // If not same url .. redirect after add to cart
                    if ($_SERVER["HTTP_REFERER"] !== Mergado\Helper\UrlHelper::getCurrentUrl()) {
                        $eventObject = new Ga4SelectItemEventObject();
                        $eventObject
                            ->setItems($eventItemsObject)
                            ->setSendTo($this->sendTo);

                        echo TemplateLoader::getTemplate(
                            __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/selectItem.php',
                            ['eventObject' => $eventObject->getResult()]
                        );
                    }
                }
            }

            $eventObject = new Ga4SelectContentEventObject('product');
            $eventObject->setSendTo($this->sendTo);

            // Change of variation
            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/selectContentVariation.php',
                ['eventObject' => $eventObject->getResult()]
            );
        }
    }

    public function removeFromCart(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            $eventObjectItems = $this->getCartEventItemsObject(true);

            $eventObject = new Ga4RemoveFromCartEventObject();
            $eventObject
                ->setValue($this->getCartValue())
                ->setCurrency(get_woocommerce_currency())
                ->setItems($eventObjectItems)
                ->setSendTo($this->sendTo);

            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/removeFromCart.php',
                ['eventObject' => $eventObject->getResult()]
            );
        }
    }

    public function viewItemList(): void
    {
        if (is_shop() || is_product_category() || is_search()) {
            if ($this->ga4Service->isActiveEcommerce()) {
                if (is_shop()) {
                    $listName = 'shop';
                } else if (is_product_category()) {
                    $listName = get_queried_object()->name;
                } else if (is_search()) {
                    $listName = 'search';
                } else {
                    $listName = '';
                }

                $eventObject = new Ga4ViewItemListEventObject();
                $eventObject->setItemListName($listName)->setSendTo($this->sendTo);

                echo TemplateLoader::getTemplate(
                    __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/viewItemList.php',
                    ['eventObject' => $eventObject->getResult(true), 'currency' => get_woocommerce_currency(), 'withVat' => $this->ga4Service->isConversionWithVat()]
                );
            }
        }
    }

    public function purchase($orderId): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();

            $orderCoupons = $this->getOrderCouponData($order);

            $eventItemsObject = new BaseGa4ItemsEventObject();

            foreach ($products_tmp as $product) {
                if ($product instanceof WC_Order_Item_Product) {
                    $productId = $product->get_product_id();
                } else {
                    $productId = $product->get_id();
                }

                if ($product->get_variation_id() == 0) {
                    $id = $productId;
                    $wcProduct = wc_get_product($id);
                    $productCoupons = $this->getOrderProductAppliedCoupons($orderCoupons, $id);
                } else {
                    $id = $productId . '-' . $product->get_variation_id();
                    $wcProduct = wc_get_product($product->get_variation_id());
                    $productCoupons = $this->getOrderProductAppliedCoupons($orderCoupons, $productId, $product->get_variation_id());
                }

                $prices = $this->getPurchaseProductPrices($product, $wcProduct);

                $eventItemObject = new BaseGa4ItemEventObject();
                $eventItemObject
                    ->setItemId((string)$id)
                    ->setItemName($product->get_name())
                    ->setItemCategories($this->getProductCategories($id))
                    ->setQuantity($product->get_quantity())
                    ->setCoupon($productCoupons)
                    ->setPrice((float)$prices['price'])
                    ->setDiscount((float)$prices['discount']);

                if ($product->get_variation_id() !== 0) {
                    $eventItemObject->setItemVariant($product->get_variation_id());
                }

                $eventItemsObject->addItem($eventItemObject);
            }

            $coupons = join(', ', $order->get_coupon_codes());

            $tax = $order->get_total_tax();
            $total = $order->get_total();

            if ($this->ga4Service->isConversionWithVat()) {
                if (!$this->ga4Service->getShippingPriceIncluded()) {
                    $total = $total - ((float)$order->get_shipping_total() + (float)$order->get_shipping_tax());
                }

                $shipping = (float)$order->get_shipping_total() + (float)$order->get_shipping_tax();
            } else {
                $total = $order->get_total() - $order->get_total_tax();
                $shipping = (float)$order->get_shipping_total();

                if (!$this->ga4Service->getShippingPriceIncluded()) {
                    $total = $total - ((float)$order->get_shipping_total());
                }
            }

            $eventObject = new Ga4PurchaseEventObject();
            $eventObject
                ->setTransactionId((string)$order->get_id())
                ->setCoupon($coupons)
                ->setValue((float)$total)
                ->setTax((float)$tax)
                ->setShipping($shipping)
                ->setCurrency(get_woocommerce_currency())
                ->setItems($eventItemsObject)
                ->setSendTo($this->sendTo);

            echo TemplateLoader::getTemplate(
                __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/purchase.php',
                ['eventObject' => $eventObject->getResult()]
            );
        }
    }

    public function pushRefundEvent(): void
    {
        try {
            $eventObject = $this->ga4Service->getRefundObject();

            if ($eventObject) {
                $this->ga4Service->deleteRefundObject(); // Remove immediatelly to prevent errors

                echo TemplateLoader::getTemplate(
                    __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/refund.php',
                    ['eventObject' => $eventObject, 'ga4id' => $this->ga4Service->getCode()]
                );
            }
        } catch (Exception $e) {
            // Don't break the page if not working
        }
    }

    public function refundFull($orderId, $refundId): void
    {
        //Change status to refunded or if all prices filled when clicked refund button
        if ($this->ga4Service->isActiveEcommerce()) {
            $order = wc_get_order($orderId);
            $alreadyRefunded = $order->get_meta(Ga4Service::REFUND_PREFIX_ORDER_FULLY_REFUNDED . $orderId, true);

            if (empty($alreadyRefunded)) {
                $order->update_meta_data(Ga4Service::REFUND_PREFIX_ORDER_FULLY_REFUNDED . $orderId, 1);
                $order->save();

                $eventObject = new Ga4RefundEventObject();
                $eventObject
                    ->setTransactionId($orderId)
                    ->setSendTo($this->sendTo);

                $this->ga4Service->setRefundObject($eventObject->getResult());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function refundPartial($orderId, $refundId): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            $data = json_decode(stripslashes($_POST['line_item_qtys']));

            $eventObjectItems = new BaseGa4ItemsEventObject();

            foreach ($data as $id => $quantity) {
                $productId = wc_get_order_item_meta($id, '_product_id', true);
                $variationId = wc_get_order_item_meta($id, '_variation_id', true);

                if ($variationId != 0) {
                    $id = $productId . '-' . $variationId;
                } else {
                    $id = $productId;
                }

                $eventItemObject = new BaseGa4ItemEventObject();
                $eventItemObject
                    ->setItemId((string)$id)
                    ->setQuantity($quantity);

                if ($variationId != 0) {
                    $eventItemObject->setItemVariant($variationId);
                }

                $eventObjectItems->addItem($eventItemObject);
            }

            // Check if products are empty ==> (products not refunded.. just discounted)
            if (isset($eventItemObject)) {
                $eventObject = new Ga4RefundEventObject();
                $eventObject
                    ->setTransactionId($orderId)
                    ->setItems($eventObjectItems)
                    ->setSendTo($this->sendTo);

                $this->ga4Service->setRefundObject($eventObject->getResult());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function orderStatusChanged($orderId, $statusOld, $statusNew, $instance): void
    {
        $order = wc_get_order($orderId);
        $alreadyRefunded = $order->get_meta(Ga4Service::REFUND_PREFIX_ORDER_FULLY_REFUNDED . $orderId, true);

        if ($this->ga4Service->isActiveEcommerce()) {

            if ($_POST['order_status'] && $this->ga4Service->isRefundStatusActive($_POST['order_status'])) {

                // Check if backend data already sent
                if (empty($alreadyRefunded)) {
                    $order->update_meta_data(Ga4Service::REFUND_PREFIX_ORDER_FULLY_REFUNDED . $orderId, 1);
                    $order->save();

                    $eventObject = new Ga4RefundEventObject();
                    $eventObject
                        ->setTransactionId($orderId)
                        ->setSendTo($this->sendTo);

                    $this->ga4Service->setRefundObject($eventObject->getResult());
                }
            }
        }
    }


    public function insertHeaderAdmin(): void
    {
        $refundObject = $this->ga4Service->getRefundObject();

        // Only for refunds now
        if ($this->ga4Service->isActiveEcommerce() && $refundObject) {
            echo TemplateLoader::getTemplate(__MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/headerAdmin.php', ['gtag4Code' => $this->getFormattedAnalyticsCode()]);
        }
    }

    public function getCartProductAppliedCoupons($productId, $variationId = null): string
    {
        $itemCoupons = [];

        $productIdForCategories = $productId;

        if ($variationId) {
            $productId = $variationId;
        }

        foreach (WC()->cart->get_coupons() as $coupon) {

            /**
             * @var $coupon WC_Coupon
             */

            $couponProductIds = $coupon->get_product_ids();
            $couponExcludedProductIds = $coupon->get_excluded_product_ids();
            $couponIncludedProductCategories = $coupon->get_product_categories();
            $couponExcludedProductCategories = $coupon->get_excluded_product_categories();

            if (
                (count($couponProductIds) === 0 || in_array($productId, $couponProductIds)) &&
                (count($couponExcludedProductIds) === 0 || !in_array($productId, $couponExcludedProductIds)) &&
                (count($couponIncludedProductCategories) === 0 || has_term($couponIncludedProductCategories, 'product_cat', $productIdForCategories)) &&
                (count($couponExcludedProductCategories) === 0 || !has_term($couponExcludedProductCategories, 'product_cat', $productIdForCategories))
            ) {
                $itemCoupons[] = $coupon->get_code();
            }
        }

        return implode(', ', $itemCoupons);
    }

    public function getOrderProductAppliedCoupons($orderCoupons, $productId, $variationId = null): string
    {
        $itemCoupons = [];

        $productIdForCategories = $productId;

        if ($variationId) {
            $productId = $variationId;
        }

        foreach ($orderCoupons as $coupon) {
            if (
                (count($coupon['included_product_ids']) === 0 || in_array($productId, $coupon['included_product_ids'])) &&
                (count($coupon['excluded_product_ids']) === 0 || !in_array($productId, $coupon['excluded_product_ids'])) &&
                (count($coupon['included_product_categories']) === 0 || has_term($coupon['included_product_categories'], 'product_cat', $productIdForCategories)) &&
                (count($coupon['excluded_product_categories']) === 0 || !has_term($coupon['excluded_product_categories'], 'product_cat', $productIdForCategories))
            ) {
                $itemCoupons[] = $coupon['code'];
            }
        }

        return implode(', ', $itemCoupons);
    }

    public function getOrderCouponData($order): array
    {
        $coupons = [];

        foreach ($order->get_coupons() as $coupon) {
            /**
             * @var $coupon WC_Order_Item_Coupon
             */

            foreach ($coupon->get_meta_data() as $meta) {
                /**
                 * @var $meta WC_Meta_Data
                 */


                if (class_exists('WooCommerce')) {
                    global $woocommerce;

                    if (version_compare($woocommerce->version, '8.7.0', '>=')) {
                        // From version 8.7.0 it was simplified string

                        $data = json_decode($meta->get_data()['value'], false);

                        $couponId = $data[0];

                        $coupon = new \WC_Coupon($couponId);

                        $couponData = $coupon->get_data();

                        $coupons[] = [
                            'code' => $couponData['code'],
                            'included_product_ids' => $couponData['product_ids'],
                            'excluded_product_ids' => $couponData['excluded_product_ids'],
                            'included_product_categories' => $couponData['product_categories'],
                            'excluded_product_categories' => $couponData['excluded_product_categories']
                        ];
                    } else {
                        // As array
                        $couponData = $meta->get_data()['value'];

                        $coupons[] = [
                            'code' => $couponData['code'],
                            'included_product_ids' => $couponData['product_ids'],
                            'excluded_product_ids' => $couponData['excluded_product_ids'],
                            'included_product_categories' => $couponData['product_categories'],
                            'excluded_product_categories' => $couponData['excluded_product_categories']
                        ];
                    }
                }
            }
        }

        return $coupons;
    }

    public function addCartData(): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            echo TemplateLoader::getTemplate(__MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/GA4/addCartData.php', ['cartData' => $this->getCartDataObject(), 'coupon' => $this->getCartGlobalCoupon()]);
        }
    }

    public function actionShippingRate($method): void
    {
        if ($this->ga4Service->isActiveEcommerce()) {
            echo sprintf('<div class="mergado-shipping-rate-label" style="display: none !important;">%s</div>', $method->get_label());
        }
    }

    public function getShippingMethods(): array
    {
        try {
            $methods = [];

            $rates = WC()->shipping()->packages;

            if (is_array($rates) && count($rates) > 0) {
                $rates = $rates[0]['rates'];
            }

            if (is_iterable($rates)) {
                foreach ($rates as $key => $shippingMethod) {
                    $methods[$key] = $shippingMethod->get_label();
                }
            }

            return $methods;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getPaymentMethods(): array
    {
        try {
            $methods = [];

            foreach (WC()->payment_gateways()->payment_gateways() as $key => $gateway) {
                $methods[$key] = $gateway->get_title();
            }

            return $methods;
        } catch (Exception $e) {
            return [];
        }
    }
}
