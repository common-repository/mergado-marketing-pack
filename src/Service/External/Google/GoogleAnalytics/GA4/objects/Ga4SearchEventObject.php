<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;

class Ga4SearchEventObject
{
    public $result = [];

    /**
     * @param string $searchTerm
     */
    public function __construct(string $searchTerm)
    {
        $this->result['search_term'] = $searchTerm;
    }

    /**
     * @throws Exception
     */
    public function getResult()
    {
        return $this->result;
    }
}
