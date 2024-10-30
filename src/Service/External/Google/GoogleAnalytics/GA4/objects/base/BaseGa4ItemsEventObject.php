<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base;

class BaseGa4ItemsEventObject
{
    public $result = [];

    /**
     * @param BaseGa4ItemEventObject $item
     * @param string|null $key
     * @return BaseGa4ItemsEventObject
     */
    public function addItem(BaseGa4ItemEventObject $item, string $key = null): self
    {
        if ($key) {
            $this->result[$key] = $item->getResult();
        } else {
            $this->result[] = $item->getResult();
        }

        return $this;
    }

    public function getResult() {
        return $this->result;
    }
}
