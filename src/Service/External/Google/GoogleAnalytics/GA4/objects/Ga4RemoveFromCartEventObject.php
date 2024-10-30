<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4EventObject;

class Ga4RemoveFromCartEventObject extends BaseGa4EventObject
{
    /**
     * @param string $coupon
     * @return Ga4RemoveFromCartEventObject
     */
    public function setCoupon(string $coupon): self
    {
        $this->result['coupon'] = $coupon;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getResult()
    {
        if (!isset($this->result['items'])) {
            if(MERGADO_DEBUG) {
                throw new Exception('Items are required in ' . __CLASS__);
            } else {
                $logger = LogService::getInstance()();
                $logger->error('Items are required in ' . __CLASS__);
            }
        }

        return $this->result;
    }
}
