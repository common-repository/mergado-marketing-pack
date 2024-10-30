<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4EventObject;
use Mergado\Service\LogService;

class Ga4AddPaymentInfoEventObject extends BaseGa4EventObject
{
    /**
     * @param string $paymentType
     * @return Ga4AddPaymentInfoEventObject
     */
    public function setPaymentType(string $paymentType): self
    {
        $this->result['payment_type'] = $paymentType;
        return $this;
    }

    /**
     * @param string $coupon
     * @return Ga4AddPaymentInfoEventObject
     */
    public function setCoupon(string $coupon): self
    {
        $this->result['coupon'] = $coupon;
        return $this;
    }

    /**
     * @param bool $withoutItems
     * @return array
     * @throws Exception
     */
    public function getResult(bool $withoutItems = false)
    {
        if (!$withoutItems && !isset($this->result['items'])) {
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
