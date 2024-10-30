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
use Mergado;
use Mergado\Exception\ZboziException;

class Zbozi
{

    /**
     * Endpoint URL
     *
     * @var string BASE_URL
     */
    const BASE_URL = 'https://%%DOMAIN%%/action/%%SHOP_ID%%/conversion/backend';

    /**
     * Private identifier of request creator
     *
     * @var string $PRIVATE_KEY
     */
    public $PRIVATE_KEY;

    /**
     * Public identifier of request creator
     *
     * @var string $SHOP_ID
     */
    public $SHOP_ID;

    /**
     * Identifier of this order
     *
     * @var string $orderId
     */
    public $orderId;

    /**
     * Customer email
     * Should not be set unless customer allows to do so.
     *
     * @var string $email
     */
    public $email;

    /**
     * How the order will be transfered to the customer
     *
     * @var string $deliveryType
     */
    public $deliveryType;

    /**
     * Cost of delivery (in CZK)
     *
     * @var float $deliveryPrice
     */
    public $deliveryPrice;

    /**
     * How the order was paid
     *
     * @var string $paymentType
     */
    public $paymentType;

    /**
     * Other fees (in CZK)
     *
     * @var string $otherCosts
     */
    public $otherCosts;

    /**
     * Array of CartItem
     *
     * @var array $cart
     */
    public $cart = array();

    /**
     * Check if string is not empty
     *
     * @param string $question String to test
     * @return boolean
     */
    private static function isNullOrEmptyString($question)
    {
        return (!isset($question) || trim($question) === '');
    }

    /**
     * Initialize Zbozi service
     *
     * @param string $shopId Shop identifier
     * @param string $privateKey Shop private key
     * @throws ZboziException can be thrown if \p $privateKey and/or \p $shopId
     * is missing or invalid.
     */
    public function __construct($shopId, $privateKey)
    {
        if ($this::isNullOrEmptyString($shopId)) {
            throw new ZboziException('shopId si mandatory');
        } else {
            $this->SHOP_ID = $shopId;
        }

        if ($this::isNullOrEmptyString($privateKey)) {
            throw new ZboziException('privateKey si mandatory');
        } else {
            $this->PRIVATE_KEY = $privateKey;
        }
    }

    /**
     * Adds ordered product using name
     *
     * @param string $productName Ordered product name
     */
    public function addProduct($productName)
    {
        $item = new ZboziCartItem();
        $item->productName = $productName;
        $this->cart[] = $item;
    }

    /**
     * Adds ordered product using item ID
     *
     * @param string $itemId Ordered product item ID
     */
    public function addProductItemId($itemId)
    {
        $item = new ZboziCartItem();
        $item->itemId = $itemId;
        $this->cart[] = $item;
    }

    /**
     * Adds ordered product using array which can contains
     * \p productName ,
     * \p itemId ,
     * \p unitPrice ,
     * \p quantity
     *
     * @param array $cartItem Array of various CartItem attributes
     */
    public function addCartItem($cartItem)
    {
        $item = new ZboziCartItem();
        if (array_key_exists("productName", $cartItem)) {
            $item->productName = $cartItem["productName"];
        }
        if (array_key_exists("itemId", $cartItem)) {
            $item->itemId = $cartItem["itemId"];
        }
        if (array_key_exists("unitPrice", $cartItem)) {
            $item->unitPrice = $cartItem["unitPrice"];
        }
        if (array_key_exists("quantity", $cartItem)) {
            $item->quantity = $cartItem["quantity"];
        }

        $this->cart[] = $item;
    }

    /**
     * Sets order attributes within
     * \p email ,
     * \p deliveryType ,
     * \p deliveryPrice ,
     * \p orderId ,
     * \p otherCosts ,
     * \p paymentType ,
     *
     * @param array $orderAttributes Array of various order attributes
     */
    public function setOrder($orderAttributes)
    {
        if (array_key_exists("email", $orderAttributes) && $orderAttributes["email"]) {
            $this->email = $orderAttributes["email"];
        }
        $this->orderId = $orderAttributes["orderId"];
        $this->paymentType = $orderAttributes["paymentType"];

        if (isset($orderAttributes["deliveryType"])) {
            $this->deliveryType = $orderAttributes["deliveryType"];
        }

        if (isset($orderAttributes["deliveryPrice"])) {
            $this->deliveryPrice = $orderAttributes["deliveryPrice"];
        }

        if (isset($orderAttributes["otherCosts"])) {
            $this->otherCosts = $orderAttributes["otherCosts"];
        }
    }


    /**
     * Creates HTTP request and returns response body
     *
     * @param string $url URL
     * @return boolean true if everything is perfect else throws exception
     * @throws ZboziException can be thrown if connection to Zbozi.cz
     * server cannot be established.
     */
    protected function sendRequest($url)
    {
        $encoded_json = json_encode(get_object_vars($this));

        if (extension_loaded('curl')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3 /* seconds */);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $response = curl_exec($ch);

            if ($response === false) {
                throw new ZboziException('Unable to establish connection to Zbozi service: ' . curl_error($ch));
            }
        } else {
            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/json",
                    'method' => 'POST',
                    'content' => $encoded_json,
                ),
            );
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            if ($response === false) {
                throw new ZboziException('Unable to establish connection to Zbozi service');
            }
        }

        $decoded_response = json_decode($response, true);
        if ((int)($decoded_response["status"] / 100) === 2) {
            return true;
        } else {
            throw new ZboziException('Request was not accepted: ' . $decoded_response['statusMessage']);
        }
    }

    /**
     * Returns endpoint URL
     *
     * @return string URL where the request will be called
     */
    private function getUrl()
    {
        $url = $this::BASE_URL;
        $url = str_replace("%%SHOP_ID%%", $this->SHOP_ID, $url);

        if (ZboziService::ZBOZI_SANDBOX) {
            $url = str_replace("%%DOMAIN%%", "sandbox.zbozi.cz", $url);
        } else {
            $url = str_replace("%%DOMAIN%%", "www.zbozi.cz", $url);
        }

        return $url;
    }

    /**
     * Sends request to Zbozi service and checks for valid response
     *
     * @return boolean true if everything is perfect else throws exception
     * @throws ZboziException can be thrown if connection to Zbozi.cz
     * server cannot be established or mandatory values are missing.
     */
    public function send()
    {
        $url = $this->getUrl();

        try {
            return $this->sendRequest($url);
        } catch (Exception $e) {
            throw new Mergado\Exception\ZboziException($e->getMessage());
        }
    }
}
