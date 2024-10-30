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

namespace Mergado\Service\External\Zbozi;

use Exception;
use Mergado\Exception\ZboziException;
use Mergado\Includes\Services\Zbozi\Zbozi;
use Mergado\Service\CookieService;
use Mergado\Service\LogService;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class ZboziServiceIntegration
{
    use SingletonTrait;

    private $zboziService;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->zboziService = ZboziService::getInstance();
        $this->cookieService = CookieService::getInstance();
    }

    // Set to order meta if user want zbozi review email
    public function setOrderMetaData($orderId): void
    {
        if (isset($_POST['zbozi-verify-checkbox']) && $_POST['zbozi-verify-checkbox']) {
            $order = wc_get_order($orderId);
            $order->update_meta_data('zbozi-verify-checkbox', esc_attr($_POST['zbozi-verify-checkbox']));
            $order->save();
        }
    }

    /**
     * Add opt-out checkbox to checkout
     *
     * @return void
     */
    public function addCheckboxVerifyOptIn(): void
    {
        if ($this->zboziService->isActive()) {
            $lang = get_locale();

            $defaultText = stripslashes($this->zboziService->getOptOut('en_US'));
            $checkboxText = stripslashes($this->zboziService->getOptOut($lang));

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = ZboziService::DEFAULT_OPT;
            }

            woocommerce_form_field('zbozi-verify-checkbox', array( // CSS ID
                'type' => 'checkbox',
                'class' => array('form-row zbozi-verify-checkbox'), // CSS Class
                'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required' => false, // Mandatory or Optional
                'label' => $checkboxText,
            ));
        }
    }

    public function conversion($orderId): void
    {
        if ($this->zboziService->isActive()) {

            $templateVariables = [
                'conversionOrderId' => $orderId,
                'conversionShopId' => $this->zboziService->getId(),
                'useSandbox' => (int)ZboziService::ZBOZI_SANDBOX,
                'consent' => (int)$this->cookieService->advertisementEnabled(),
                'cookieName' => "_{$this->zboziService->getId()}_{$orderId}"
            ];

            if ($this->zboziService->isAdvanced()) {
                $templateVariables['scriptUrl'] = 'https://www.zbozi.cz/conversion/js/conv-v3.js';
            } else {
                $templateVariables['scriptUrl'] = 'https://www.zbozi.cz/conversion/js/conv.js';
            }

            echo TemplateLoader::getTemplate(__DIR__ . '/templates/conversion.php', $templateVariables);
        }
    }

    public function submitOrderToZbozi($orderId): bool
    {
        $order = wc_get_order($orderId);
        $confirmed = $order->get_meta('zbozi-verify-checkbox', true);

        $id = $this->zboziService->getId();
        $secret = $this->zboziService->getKey();
        $zboziWithVat = $this->zboziService->isConversionWithVat();

        if ($this->zboziService->isActive()) {
            if ($this->zboziService->isAdvanced()) {
                // Extended process
                try {
                    $zbozi = new \Mergado\Service\External\Zbozi\Zbozi($id, $secret);

                    $products = $order->get_items();

                    foreach ($products as $product) {
                        $pid = $product->get_data()['product_id'];

                        if ($product->get_data()['variation_id'] != 0) {
                            $pid .= '-' . $product->get_data()['variation_id'];
                        }

                        // Product price is for all products
                        if ($zboziWithVat) {
                            $productPrice = (float)($product->get_total() + $product->get_total_tax());
                        } else {
                            $productPrice = (float)$product->get_total();
                        }

                        $quantity = $product->get_quantity();

                        $zbozi->addCartItem(array(
                            'itemId' => $pid,
                            'productName' => $product->get_name(),
                            'unitPrice' => ($productPrice / $quantity),
                            'quantity' => $quantity,
                        ));
                    }

                    if ($zboziWithVat) {
                        $deliveryPrice = $order->get_shipping_total() + $order->get_shipping_tax();
                        $otherCosts = $order->get_total_discount(false);
                    } else {
                        $deliveryPrice = $order->get_shipping_total();
                        $otherCosts = $order->get_total_discount(true);
                    }

                    $zboziOrderData = [
                        'orderId' => $orderId,
                        'paymentType' => $order->get_payment_method(),
                    ];

                    if (empty($confirmed)) {
                        $zboziOrderData['email'] = $order->get_billing_email();
                    }

                    if ($order->needs_shipping_address()) {
                        $zboziOrderData['deliveryType'] = ZboziDeliveryType::getDeliveryType($order->get_shipping_method());
                        $zboziOrderData['deliveryPrice'] = $deliveryPrice;
                        $zboziOrderData['otherCosts'] = $otherCosts;
                    }

                    $zbozi->setOrder($zboziOrderData);

                    $zbozi->send();

                    return true;
                } catch (ZboziException|Exception $e) {
                    $logger = LogService::getInstance();
                    $logger->error('[ZBOZI]: Error: ' . $e->getMessage());
                }
            }
        }

        return true;
    }
}
