<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4EventObject;

class Ga4ViewItemEventObject extends BaseGa4EventObject
{
    /**
     * @throws Exception
     */
    public function getResult()
    {
        if (!isset($this->result['items'])) {
            throw new Exception('Items are required in ' . get_class());
        }

        return $this->result;
    }
}
