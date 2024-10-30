<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;

class Ga4SelectContentEventObject
{
    public $result = [];

    /**
     * @param string $contentType
     * @param string|null $itemId
     */
    public function __construct(string $contentType, string $itemId = null)
    {
        $this->result['content_type'] = $contentType;

        if ($itemId) {
            $this->result['item_id'] = $itemId;
        }

        return $this;
    }

    /**
     * @param string $sendTo
     * @return Ga4SelectContentEventObject
     */
    public function setSendTo(string $sendTo): self
    {
        $this->result['send_to'] = $sendTo;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getResult()
    {
        return $this->result;
    }
}
