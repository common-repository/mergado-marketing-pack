<?php

namespace Mergado\Feed\Customer;

use DOMException;
use Exception;
use Mergado\Feed\Shared\AbstractFeedItem;
use Mergado\Service\LogService;

class CustomerFeedItem extends AbstractFeedItem
{
    private $logger;
    public $customerId;
    public $customerFirstName;
    public $customerLastName;
    public $customerEmail;

    /**
     * Billing address
     */
    public $billingFirstName;
    public $billingLastName;
    public $billingCompany;
    public $billingAddress1;
    public $billingAddress2;
    public $billingCity;
    public $billingZip;
    public $billingCountry;
    public $billingState;
    public $billingEmail;
    public $billingPhone;

    /**
     * Shipping address
     */
    public $shippingFirstName;
    public $shippingLastName;
    public $shippingCompany;
    public $shippingAddress1;
    public $shippingAddress2;
    public $shippingCity;
    public $shippingZip;
    public $shippingCountry;
    public $shippingState;
    public $shippingPhone;

    public function __construct() {
        parent::__construct();

        $this->logger = LogService::getInstance();
    }

    /**
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception("Cannot add new property \$$name to instance of " . __CLASS__ . '. Use set methods instead!');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId): CustomerFeedItem
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }

    /**
     * @param mixed $customerFirstName
     */
    public function setCustomerFirstName($customerFirstName): CustomerFeedItem
    {
        $this->customerFirstName = $customerFirstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }

    /**
     * @param mixed $customerLastName
     */
    public function setCustomerLastName($customerLastName): CustomerFeedItem
    {
        $this->customerLastName = $customerLastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @param mixed $customerEmail
     */
    public function setCustomerEmail($customerEmail): CustomerFeedItem
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingFirstName()
    {
        return $this->billingFirstName;
    }

    /**
     * @param mixed $billingFirstName
     */
    public function setBillingFirstName($billingFirstName): CustomerFeedItem
    {
        $this->billingFirstName = $billingFirstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingLastName()
    {
        return $this->billingLastName;
    }

    /**
     * @param mixed $billingLastName
     */
    public function setBillingLastName($billingLastName): CustomerFeedItem
    {
        $this->billingLastName = $billingLastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingCompany()
    {
        return $this->billingCompany;
    }

    /**
     * @param mixed $billingCompany
     */
    public function setBillingCompany($billingCompany): CustomerFeedItem
    {
        $this->billingCompany = $billingCompany;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingAddress1()
    {
        return $this->billingAddress1;
    }

    /**
     * @param mixed $billingAddress1
     */
    public function setBillingAddress1($billingAddress1): CustomerFeedItem
    {
        $this->billingAddress1 = $billingAddress1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingAddress2()
    {
        return $this->billingAddress2;
    }

    /**
     * @param mixed $billingAddress2
     */
    public function setBillingAddress2($billingAddress2): CustomerFeedItem
    {
        $this->billingAddress2 = $billingAddress2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * @param mixed $billingCity
     */
    public function setBillingCity($billingCity): CustomerFeedItem
    {
        $this->billingCity = $billingCity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingZip()
    {
        return $this->billingZip;
    }

    /**
     * @param mixed $billingZip
     */
    public function setBillingZip($billingZip): CustomerFeedItem
    {
        $this->billingZip = $billingZip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    /**
     * @param mixed $billingCountry
     */
    public function setBillingCountry($billingCountry): CustomerFeedItem
    {
        $this->billingCountry = $billingCountry;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingState()
    {
        return $this->billingState;
    }

    /**
     * @param mixed $billingState
     */
    public function setBillingState($billingState): CustomerFeedItem
    {
        $this->billingState = $billingState;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * @param mixed $billingEmail
     */
    public function setBillingEmail($billingEmail): CustomerFeedItem
    {
        $this->billingEmail = $billingEmail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingPhone()
    {
        return $this->billingPhone;
    }

    /**
     * @param mixed $billingPhone
     */
    public function setBillingPhone($billingPhone): CustomerFeedItem
    {
        $this->billingPhone = $billingPhone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingFirstName()
    {
        return $this->shippingFirstName;
    }

    /**
     * @param mixed $shippingFirstName
     */
    public function setShippingFirstName($shippingFirstName): CustomerFeedItem
    {
        $this->shippingFirstName = $shippingFirstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingLastName()
    {
        return $this->shippingLastName;
    }

    /**
     * @param mixed $shippingLastName
     */
    public function setShippingLastName($shippingLastName): CustomerFeedItem
    {
        $this->shippingLastName = $shippingLastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingCompany()
    {
        return $this->shippingCompany;
    }

    /**
     * @param mixed $shippingCompany
     */
    public function setShippingCompany($shippingCompany): CustomerFeedItem
    {
        $this->shippingCompany = $shippingCompany;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingAddress1()
    {
        return $this->shippingAddress1;
    }

    /**
     * @param mixed $shippingAddress1
     */
    public function setShippingAddress1($shippingAddress1): CustomerFeedItem
    {
        $this->shippingAddress1 = $shippingAddress1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingAddress2()
    {
        return $this->shippingAddress2;
    }

    /**
     * @param mixed $shippingAddress2
     */
    public function setShippingAddress2($shippingAddress2): CustomerFeedItem
    {
        $this->shippingAddress2 = $shippingAddress2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    /**
     * @param mixed $shippingCity
     */
    public function setShippingCity($shippingCity): CustomerFeedItem
    {
        $this->shippingCity = $shippingCity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingZip()
    {
        return $this->shippingZip;
    }

    /**
     * @param mixed $shippingZip
     */
    public function setShippingZip($shippingZip): CustomerFeedItem
    {
        $this->shippingZip = $shippingZip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingCountry()
    {
        return $this->shippingCountry;
    }

    /**
     * @param mixed $shippingCountry
     */
    public function setShippingCountry($shippingCountry): CustomerFeedItem
    {
        $this->shippingCountry = $shippingCountry;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingState()
    {
        return $this->shippingState;
    }

    /**
     * @param mixed $shippingState
     */
    public function setShippingState($shippingState): CustomerFeedItem
    {
        $this->shippingState = $shippingState;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingPhone()
    {
        return $this->shippingPhone;
    }

    /**
     * @param mixed $shippingPhone
     */
    public function setShippingPhone($shippingPhone): CustomerFeedItem
    {
        $this->shippingPhone = $shippingPhone;
        return $this;
    }

    public function getItemXml()
    {
        try {
            $item = $this->xml->createElement('ITEM');

            $this->createXmlItemProperty($item, 'CUSTOMER_ID', $this->getCustomerId());
            $this->createXmlItemProperty($item, 'CUSTOMER_FIRSTNAME', $this->getCustomerFirstName());
            $this->createXmlItemProperty($item, 'CUSTOMER_LASTNAME', $this->getCustomerLastName());
            $this->createXmlItemProperty($item, 'EMAIL', $this->getCustomerEmail());

            $billingAddress = $this->xml->createElement('BILLING_ADDRESS');

            $this->createXmlItemProperty($billingAddress, 'FIRSTNAME', $this->getBillingFirstName());
            $this->createXmlItemProperty($billingAddress, 'LASTNAME', $this->getBillingLastName());
            $this->createXmlItemProperty($billingAddress, 'COMPANY', $this->getBillingCompany());
            $this->createXmlItemProperty($billingAddress, 'ADDRESS_1', $this->getBillingAddress1());
            $this->createXmlItemProperty($billingAddress, 'ADDRESS_2', $this->getBillingAddress2());
            $this->createXmlItemProperty($billingAddress, 'CITY', $this->getBillingCity());
            $this->createXmlItemProperty($billingAddress, 'ZIP', $this->getBillingZip());
            $this->createXmlItemProperty($billingAddress, 'COUNTRY', $this->getBillingCountry());
            $this->createXmlItemProperty($billingAddress, 'STATE', $this->getBillingState());
            $this->createXmlItemProperty($billingAddress, 'PHONE', $this->getBillingPhone());
            $this->createXmlItemProperty($billingAddress, 'EMAIL', $this->getBillingEmail());

            $item->appendChild($billingAddress);

            $shippingAddress = $this->xml->createElement('SHIPPING_ADDRESS');

            $this->createXmlItemProperty($shippingAddress, 'FIRSTNAME', $this->getShippingFirstName());
            $this->createXmlItemProperty($shippingAddress, 'LASTNAME', $this->getShippingLastName());
            $this->createXmlItemProperty($shippingAddress, 'COMPANY', $this->getShippingCompany());
            $this->createXmlItemProperty($shippingAddress, 'ADDRESS_1', $this->getShippingAddress1());
            $this->createXmlItemProperty($shippingAddress, 'ADDRESS_2', $this->getShippingAddress2());
            $this->createXmlItemProperty($shippingAddress, 'CITY', $this->getShippingCity());
            $this->createXmlItemProperty($shippingAddress, 'ZIP', $this->getShippingZip());
            $this->createXmlItemProperty($shippingAddress, 'COUNTRY', $this->getShippingCountry());
            $this->createXmlItemProperty($shippingAddress, 'STATE', $this->getShippingState());
            $this->createXmlItemProperty($shippingAddress, 'PHONE', $this->getShippingPhone());

            $item->appendChild($shippingAddress);

            $this->xml->appendChild($item);

            return $this->xml;
        } catch (DOMException $e) {
            $this->logger->error('Error creating CustomerFeedItem of customer ID: ' . $this->getCustomerId(), CustomerFeed::getLogContext());
        }

        return false;
    }
}

