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

namespace Mergado\Service\External\Heureka;

use Exception;
use Mergado\Helper\LanguageHelper;
use Mergado\Service\CookieService;
use Mergado\Service\LogService;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class HeurekaServiceIntegration
{
    use SingletonTrait;

    private $heurekaCzService;
    private $heurekaSkService;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->heurekaCzService = HeurekaCzService::getInstance();
        $this->heurekaSkService = HeurekaSkService::getInstance();
        $this->cookieService = CookieService::getInstance();
    }

    public function getWidgetTemplate(): void
    {
        $lang = LanguageHelper::getLang();
        $langLower = strtolower($lang);
        $service = $this->getActiveService();

        if ($service && $service->isWidgetActive()) {
            echo TemplateLoader::getTemplate(__DIR__ . '/templates/widget.php', [
                'widgetId' => $service->getWidgetId(),
                'langLower' => $langLower,
                'marginTop' => $service->getWidgetTopMargin(),
                'position' => $service->getWidgetPosition(),
                'functionalCookiesEnabled' => $this->cookieService->functionalEnabled()
            ]);
        }
    }

    public function conversion($orderId): void
    {
        $lang = LanguageHelper::getLang();
        $langLower = strtolower($lang);
        $service = $this->getActiveService();

        if ($service && $service->isConversionActive()) {
            $products = $this->getProducts($orderId);

            if (count($products)) {
                echo TemplateLoader::getTemplate(__DIR__ . '/templates/conversion.php', [
                    'lang' => $langLower,
                    'code' => $service->getConversionCode(),
                    'products' => $products,
                    'orderId' => $orderId,
                ]);
            }
        }
    }

    /**
     * Send data from backend to Heureka
     */
    public function submitVerify($orderId): void
    {
        try {
            $order = wc_get_order($orderId);
            $confirmed = $order->get_meta('heureka-verify-checkbox', true);

            if (empty($confirmed)) {
                $service = $this->getActiveService();

                if ($service && $service->isVerifiedActive()) {
                    $url = $this->getRequestURL($service->getUrl(),$service->getVerifiedCode(), $orderId);
                    $this->sendRequest($url);
                }
            }
        } catch (Exception $e) {
            $logger = LogService::getInstance();
            $logger->error('Heureka verify - ' . $e->getMessage(), 'heureka');
        }
    }

    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    private function getProducts($orderId): array
    {
        $service = $this->getActiveService();
        $order = wc_get_order($orderId);

        $products = [];

        foreach ($order->get_items() as $item) {
            if ($service->isConversionWithVat()) {
                $unitPrice = ($item->get_total() + $item->get_total_tax()) / $item->get_quantity();
            } else {
                $unitPrice = $item->get_total() / $item->get_quantity();
            }

            $product = [
                'name' => $item->get_name(),
                'qty' => $item->get_quantity(),
                'unitPrice' => $unitPrice,
            ];

            if ($item->get_data()['variation_id'] == 0) {
                $product['id'] = $item->get_data()['product_id'];
            } else {
                $product['id'] = $item->get_data()['variation_id'];
            }

            $products[] = $product;
        }

        return $products;
    }

    private function getRequestURL($url, $apiKey, $orderId): string
    {
        $order = wc_get_order($orderId);

        $url .= '?id=' . $apiKey;
        $url .= '&email=' . urlencode($order->get_billing_email());

        $products = $order->get_items();

        foreach ($products as $product) {

            $exactName = $product->get_name();

            $url .= '&produkt[]=' . urlencode($exactName);
            if ($product->get_variation_id() == 0) {
                $url .= '&itemId[]=' . urlencode($product->get_data()['product_id']);
            } else {
                $url .= '&itemId[]=' . urlencode($product->get_variation_id());
            }
        }

        $url .= '&orderid=' . urlencode($orderId);

        return $url;
    }

    /**
     * SEND
     */

    /**
     * Send heureka request
     * @throws Exception
     */
    private function sendRequest($url): string
    {
        try {
            $parsed = parse_url($url);
            $fp = fsockopen($parsed['host'], 80, $errno, $errstr, 5);

            if (!$fp) {
                throw new Exception($errstr . ' (' . $errno . ')');
            } else {
                $return = '';
                $out = 'GET ' . $parsed['path'] . '?' . $parsed['query'] . " HTTP/1.1\r\n" .
                    'Host: ' . $parsed['host'] . "\r\n" .
                    "Connection: Close\r\n\r\n";
                fputs($fp, $out);
                while (!feof($fp)) {
                    $return .= fgets($fp, 128);
                }
                fclose($fp);
                $returnParsed = explode("\r\n\r\n", $return);

                return empty($returnParsed[1]) ? '' : trim($returnParsed[1]);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /*******************************************************************************************************************
     * HELPERS
     *******************************************************************************************************************/

    private function getActiveService()
    {
        $lang = LanguageHelper::getLang();

        $service = false;

        if (in_array($lang, ['CZ', 'SK'])) {
            if ($lang === 'CZ') {
                $service = $this->heurekaCzService;
            } else if ($lang === 'SK') {
                $service = $this->heurekaSkService;
            }
        }

        return $service;
    }

    public function addVerifyOptOutCheckbox(): void
    {
        $service = $this->getActiveService();

        if ($service && $service->isVerifiedActive()) {
            $lang = get_locale();

            $defaultText = stripslashes(get_option('heureka-verify-opt-out-text-en_US', 0));
            $checkboxText = stripslashes(get_option('heureka-verify-opt-out-text-' . $lang, 0));

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = BaseHeurekaService::DEFAULT_OPT;
            }

            woocommerce_form_field('heureka-verify-checkbox', array( // CSS ID
                'type' => 'checkbox',
                'class' => array('form-row heureka-verify-checkbox'), // CSS Class
                'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required' => false, // Mandatory or Optional
                'label' => $checkboxText,
            ));
        }
    }

    /**
     * Set order meta if user want heureka review email
     */
    public function setOrderMetaData($orderId): void
    {
        if (isset($_POST['heureka-verify-checkbox']) && $_POST['heureka-verify-checkbox']) {
            $order = wc_get_order($orderId);
            $order->update_meta_data('heureka-verify-checkbox', esc_attr($_POST['heureka-verify-checkbox']));
            $order->save();
        }
    }
}
