<?php

namespace Mergado\Service\External\Sklik;

use Mergado\Service\CookieService;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class SklikServiceIntegration
{
    use SingletonTrait;

    /**
     * @var SklikService
     */
    private $sklikService;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->sklikService = SklikService::getInstance();
        $this->cookieService = CookieService::getInstance();
    }

    public function conversion($orderId): void
    {
        $conversionValue = $this->sklikService->getConversionValue();

        if ($this->sklikService->isConversionActive()) {
            if ($conversionValue === '') {
                $order = wc_get_order($orderId);

                if ($this->sklikService->isConversionWithVat()) {
                    $conversionValue = number_format((float)$order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
                } else {
                    //Price of items
                    $conversionValue = number_format((float)$order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '');
                }
            }

            $this->insertCustomerInfoTemplate();

            echo TemplateLoader::getTemplate(__DIR__ . '/templates/conversion.php', [
                'conversionId' => $this->sklikService->getConversionCode(),
                'conversionValue' => $conversionValue,
                'consent' => (int)$this->cookieService->advertisementEnabled()
            ]);
        }
    }

    public function retargeting(): void
    {
        if ($this->sklikService->isRetargetingActive()) {
            $this->insertCustomerInfoTemplate();

            echo TemplateLoader::getTemplate(__DIR__ . '/templates/retargeting.php', [
                'retargetingId' => $this->sklikService->getRetargetingId(),
                'consent' => (int)$this->cookieService->advertisementEnabled()
            ]);
        }
    }

    private function insertCustomerInfoTemplate(): void
    {
        $email = null;
        $phone = null;

        if (is_order_received_page()) {
            global $wp;

            $order = wc_get_order($wp->query_vars['order-received']);

            $customer = $order->get_user();

            if ($customer) {
                $email = $customer->get('user_email');
            } else {
                $email = $order->get_billing_email();
            }

            $phone = $order->get_billing_phone();

            if (trim($phone) === '') {
                $phone = null;
            }
        } else if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $email = $current_user->user_email;
            $phone = get_user_meta( $current_user->id, 'billing_phone', true );
        }

        echo TemplateLoader::getTemplate(__DIR__ . '/templates/customerInfo.php', [
            'phone' => !empty($phone) ? $phone : null,
            'email' => !empty($email) ? $email : null,
        ]);
    }
}
