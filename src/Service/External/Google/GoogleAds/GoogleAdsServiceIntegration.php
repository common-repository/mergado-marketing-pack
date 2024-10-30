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

namespace Mergado\Service\External\Google\GoogleAds;

use Mergado;
use Mergado\Service\External\Google\Gtag\GtagIntegrationHelper;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;
use WC_Order;

class GoogleAdsServiceIntegration
{
    use SingletonTrait;

    /**
     * @var GoogleAdsService
     */
    private $googleAdsService;
    /**
     * @var false|string|null
     */
    private $sendTo;

    public function __construct()
    {
        $this->googleAdsService = GoogleAdsService::getInstance();
        $this->sendTo = $this->googleAdsService->getConversionCode();
    }

    public function conversion($order_id): void
    {
        $order = wc_get_order($order_id);

        $active = $this->googleAdsService->isConversionActive();
        $code = $this->googleAdsService->getConversionCode();
        $label = $this->googleAdsService->getConversionLabel();

        $orderTotal = $this->getTotalOrderPrice($order);

        $customer = $order->get_user();

        if ($customer) {
            $email = $customer->get('user_email');
        } else {
            $email = $order->get_billing_email();
        }

        $phone = $order->get_billing_phone();

        if (trim($phone) === '') {
            $phone = false;
        }

        if ($active) {
            $templatePath = __DIR__ . '/templates/conversion.php';

            $templateVariables = [
                'enhancedConversionsActive' => $this->googleAdsService->getEnhancedConversionsActive(),
                'userEmail' => $email,
                'userPhone' => $phone,
                'sendTo' => $code . '/' . $label,
                'value' => $orderTotal,
                'currency' => get_woocommerce_currency(),
                'transactionId' => $order_id,
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    public function productDetailView(): void
    {
        if ($this->googleAdsService->isRemarketingActive()) {
            GtagIntegrationHelper::productDetailView($this->sendTo, $this->googleAdsService->isConversionWithVat());
        }
    }

    public function viewItemList(): void
    {
        if (is_shop() || is_product_category() || is_search()) {
            if ($this->googleAdsService->isRemarketingActive()) {
                GtagIntegrationHelper::viewItemList($this->sendTo, $this->googleAdsService->isConversionWithVat());
            }
        }
    }

    public function addToCart() : string
    {
        if ($this->googleAdsService->isRemarketingActive()) {
            return GtagIntegrationHelper::addToCart($this->sendTo, $this->googleAdsService->isConversionWithVat());
        }

        return '';
    }

    public function addToCartAjax(): void
    {
        if ($this->googleAdsService->isRemarketingActive()) {
            GtagIntegrationHelper::addToCartAjax($this->sendTo, $this->googleAdsService->isConversionWithVat());
        }
    }

    /*
     * Helper functions
     */

    public function getTotalOrderPrice(WC_Order $order): float
    {
        $total = $order->get_total();

        if ($this->googleAdsService->isConversionWithVat()) {
            if (!$this->googleAdsService->isShippingPriceIncluded()) {
                $total = $total - ((float)$order->get_shipping_total() + (float)$order->get_shipping_tax());
            }

        } else {
            $total = $order->get_total() - $order->get_total_tax();

            if (!$this->googleAdsService->isShippingPriceIncluded()) {
                $total = $total - ((float)$order->get_shipping_total());
            }
        }

        return $total;
    }

}
