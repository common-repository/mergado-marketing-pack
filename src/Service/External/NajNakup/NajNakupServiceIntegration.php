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

namespace Mergado\Service\External\NajNakup;

use Exception;
use Mergado\Traits\SingletonTrait;

class NajNakupServiceIntegration
{
    use SingletonTrait;

    /**
     * Send data from backend to NajNakup
     */
    public function sendValuation($orderId)
    {
        $najNakupService = NajNakupService::getInstance();

        $active = $najNakupService->isActive();
        $code = $najNakupService->getId();

        if ($active) {
            try {
                $order = wc_get_order($orderId);
                $email = $order->get_billing_email();
                $products = $order->get_items();

                $orderProducts = [];

                foreach ($products as $product) {
                    if ($product->get_variation_id() == 0) {
                        $orderProducts[] = $product->get_data()['product_id'];
                    } else {
                        $orderProducts[] = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    }
                }

                return $this->sendNewOrder($code, $email, $orderId, $orderProducts);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return false;
    }

    /**
     * Send new order to najnakup.sk
     *
     * @throws Exception
     */
    public function sendNewOrder($shopId, $email, $shopOrderId, $products)
    {
        $url = 'http://www.najnakup.sk/dz_neworder.aspx' . '?w=' . $shopId;
        $url .= '&e=' . urlencode($email);
        $url .= '&i=' . urlencode($shopOrderId);

        foreach ($products as $product) {
            $url .= '&p=' . urlencode($product);
        }

        $contents = $this->submitRequest($url, "www.najnakup.sk", "80");

        if ($contents === false) {
            throw new Exception('Neznama chyba');
        } elseif ($contents !== '') {
            return $contents;
        } else {
            throw new Exception($contents);
        }
    }

    /**
     * Sends request to najnakup.sk
     *
     * @throws Exception
     */
    private function submitRequest($url, $host, $port)
    {
        $fp = fsockopen($host, $port, $errno, $errstr, 6);

        if (!$fp) {
            throw new Exception($errstr . ' (' . $errno . ')');
        } else {
            $return = '';
            $out = "GET " . $url;
            $out .= " HTTP/1.1\r\n";
            $out .= "Host: " . $host . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);

            while (!feof($fp)) {
                $return .= fgets($fp, 128);
            }

            fclose($fp);
            $rp1 = explode("\r\n\r\n", $return);
            return $rp1[count($rp1) - 1] == '0' ? '' : $rp1[count($rp1) - 1];
        }
    }
}
