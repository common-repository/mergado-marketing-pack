<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4EventObject;
use Mergado\Service\LogService;

class Ga4AddToCartEventObject extends BaseGa4EventObject
{
    /**
     * @param bool $withoutItems
     * @return array
     * @throws Exception
     */
    public function getResult(bool $withoutItems = false): array
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
