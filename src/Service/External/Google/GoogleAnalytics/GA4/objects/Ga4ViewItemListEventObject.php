<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base\BaseGa4ItemsEventObject;
use Mergado\Service\LogService;

class Ga4ViewItemListEventObject
{
    /**
     * @var array
     */
    protected $result = [];

    /**
     * @param string $itemListId
     * @return Ga4ViewItemListEventObject
     */
    public function setItemListId(string $itemListId): self
    {
        $this->result['item_list_id'] = $itemListId;
        return $this;
    }

    /**
     * @param string $itemListName
     * @return Ga4ViewItemListEventObject
     */
    public function setItemListName(string $itemListName): self
    {
        $this->result['item_list_name'] = $itemListName;
        return $this;
    }

    /**
     * @param BaseGa4ItemsEventObject $items
     * @return Ga4ViewItemListEventObject
     */
    public function setItems(BaseGa4ItemsEventObject $items): self
    {
        $this->result['items'] = $items;
        return $this;
    }

    /**
     * @param string $sendTo
     * @return Ga4ViewItemListEventObject
     */
    public function setSendTo(string $sendTo): self
    {
        $this->result['send_to'] = $sendTo;
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
