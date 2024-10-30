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

namespace Mergado\Service\External\Google\GoogleAnalytics;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalService;
use Mergado\Service\LogService;
use Mergado\Traits\SingletonTrait;

class GoogleAnalyticsRefundService
{
    use SingletonTrait;

    public const STATUS = 'ga_refund_status';

    public function isStatusActive($statusKey): bool
    {
        $active = $this->getStatus($statusKey);

        return $active === 1;
    }


    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getStatus($statusKey): int
    {
        // Default set to true
        if ($statusKey === 'wc-refunded') {
            $result = get_option(self::STATUS . $statusKey, 1);
        } else {
            $result = get_option(self::STATUS . $statusKey, 0);
        }

	    return (int) $result;
    }

    public function sendRefundCode($products, $orderId, $partial = false)
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_URL => $this->createRefundUrl($products, $orderId, $partial),
        ));

        $response = curl_exec($ch);
        $errorCount = curl_errno($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false || $errorCount > 0) {
            LogService::getInstance()->warning('Google refund error - ' . $error);
            return true;
        }

        $decoded_response = json_decode($response, true);

        if ((int)($decoded_response["status"] / 100) === 2) {
            return true;
        }
    }

    private function createRefundUrl($products, $orderId, $partial = false): string
    {
        $GaUniversalService = GaUniversalService::getInstance();

        $data = array(
            'v' => '1', // Version.
            'tid' => $GaUniversalService->getCode(), // Tracking ID / Property ID.
            'cid' => '35009a79-1a05-49d7-b876-2b884d0f825b', // Anonymous Client ID
            't' => 'event', // Event hit type.
            'ec' => 'Ecommerce', // Event Category. Required.
            'ea' => 'Refund', // Event Action. Required.
            'ni' => '1', // Non-interaction parameter.
            'ti' => $orderId, // Transaction ID,
            'pa' => 'refund',
        );

        if ($partial) {
            $counter = 1;
            foreach($products as $id => $quantity) {
                $data['pr' . $counter . 'id'] = $id;
                $data['pr' . $counter . 'qt'] = $quantity;
                $counter++;
            }
        }

//        $url = 'https://www.google-analytics.com/debug/collect?';
        $url = 'https://www.google-analytics.com/collect?';
        $url .= http_build_query($data);

        return $url;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

	public static function saveFields(array $post): void
    {
	    $checkbox = [];

        foreach (wc_get_order_statuses() as $key => $data) {
            $checkbox[] = self::STATUS . $key;
        }

        DatabaseManager::saveOptions($post,
            $checkbox);
	}
}
