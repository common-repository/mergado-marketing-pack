<?php

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4\objects\base;

class BaseGa4EventObject
{
    /**
     * @var array
     */
    protected $result = [];

    /**
     * @param string $currency
     * @return BaseGa4EventObject
     */
    public function setCurrency(string $currency): self
    {
        $this->result['currency'] = $currency;
        return $this;
    }

    /**
     * @param $value
     * @return BaseGa4EventObject
     */
    public function setValue($value): self
    {
        $this->result['value'] = $value;
        return $this;
    }

    /**
     * @param BaseGa4ItemsEventObject $items
     * @return BaseGa4EventObject
     */
    public function setItems(BaseGa4ItemsEventObject $items): self
    {
        $this->result['items'] = $items->getResult();
        return $this;
    }

    /**
     * @param string $sendTo
     * @return BaseGa4EventObject
     */
    public function setSendTo(string $sendTo): self
    {
        $this->result['send_to'] = $sendTo;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }
}
