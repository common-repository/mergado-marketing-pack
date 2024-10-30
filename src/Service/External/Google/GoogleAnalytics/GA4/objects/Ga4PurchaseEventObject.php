<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4EventObject;
use Mergado\Service\LogService;

class Ga4PurchaseEventObject extends BaseGa4EventObject
{
    /**
     * @param string $transactionId
     * @return Ga4PurchaseEventObject
     */
    public function setTransactionId(string $transactionId) : self
    {
        $this->result['transaction_id'] = $transactionId;
        return $this;
    }

    /**
     * @param string $affiliation
     * @return Ga4PurchaseEventObject
     */
    public function setAffiliation(string $affiliation) : self
    {
        $this->result['affiliation'] = $affiliation;
        return $this;
    }

    /**
     * @param string $coupon
     * @return Ga4PurchaseEventObject
     */
    public function setCoupon(string $coupon) : self
    {
        $this->result['coupon'] = $coupon;
        return $this;
    }

    /**
     * @param mixed $shipping
     * @return Ga4PurchaseEventObject
     */
    public function setShipping($shipping) : self
    {
        $this->result['shipping'] = $shipping;
        return $this;
    }

    /**
     * @param mixed $tax
     * @return Ga4PurchaseEventObject
     */
    public function setTax($tax) : self
    {
        $this->result['tax'] = $tax;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getResult()
    {
        if (!isset($this->result['transaction_id'])) {
            if(MERGADO_DEBUG) {
                throw new Exception('Transaction Id are required in ' . __CLASS__);
            } else {
                $logger = LogService::getInstance();
                $logger->error('Transaction Id are required in ' . __CLASS__);
            }
        }

        if (!isset($this->result['items'])) {
            if(MERGADO_DEBUG) {
                throw new Exception('Items are required in ' . __CLASS__);
            } else {
                $logger = LogService::getInstance();
                $logger->error('Items are required in ' . __CLASS__);
            }
        }

        return $this->result;
    }
}
