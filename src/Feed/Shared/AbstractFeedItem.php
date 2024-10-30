<?php

namespace Mergado\Feed\Shared;

use DOMDocument;

abstract class AbstractFeedItem implements FeedItemInterface
{
    protected $xml;
    public function __construct()
    {
        $this->xml = new DOMDocument('1.0', 'UTF-8');
    }

    /**
     *
     * $customProperties should be ARRAY in one of following formats
     *
     * =======================
     * === Simple property ===
     * =======================
     *
     * ['propertyName' => 'propertyValue']
     *
     * =======================
     * === Nested property ===
     * =======================
     *
     * ['propertyName' => ['propertyName' => 'propertyValue']]
     *
     * =========================================================
     * === Multi-nested property (infinite nesting possible) ===
     * =========================================================
     *
     * ['propertyName' => ['propertyName' => ['propertyName' => 'propertyValue']]]
     *
     *
     * IMPORTANT INFO: Values that contains something that XML parser can read as markup should be wrapped in <![CDATA[]]>
     *
     * @param $item
     * @param $name
     * @param $value
     * @return mixed
     * @throws \DOMException
     */
    protected function createCustomProperties(&$item, $name, $value) {
        if (is_array($value)) {
            $element = $this->xml->createElement($name);

            foreach($value as $propName => $propValue) {
                $element->appendChild($this->createCustomProperties($element, $propName, $propValue));
            }


            return $item->appendChild($element);
        }

        return $item->appendChild($this->xml->createElement($name, $value));
    }

    /**
     * @param $item
     * @param $name
     * @param $value
     * @param bool $sanitizeCData
     * @param bool $showIfEmpty
     * @return mixed
     * @throws \DOMException
     */
    protected function createXmlItemProperty($item, $name, $value, bool $sanitizeCData = false, bool $showIfEmpty = false) {
        if (!$showIfEmpty && ($value === null || trim($value) === '')) {
            return false;
        }

        if ($sanitizeCData) {
            $element = $this->xml->createElement($name);
            $element->appendChild($this->xml->createCDATASection($value));
            $item->appendChild($element);
        } else {
            $item->appendChild($this->xml->createElement($name, $value));
        }

        return $item;
    }

    /**
     * @param $item
     * @param $name
     * @param $values
     * @param bool $sanitizeCData
     * @return mixed|void
     * @throws \DOMException
     */
    protected function createXmlItemPropertyArray($item, $name, $values, bool $sanitizeCData = false) {
        if (!is_array($values) || count($values) <= 0) {
            return false;
        }

        foreach ($values as $value) {
            if ($sanitizeCData) {
                $element = $this->xml->createElement($name);
                $element->appendChild($this->xml->createCDATASection($value));
                $item->appendChild($element);
            } else {
                $item->appendChild($this->xml->createElement($name, $value));
            }
        }

        return $item;
    }

    /**
     * @param $item
     * @param $values
     * @return mixed|void
     * @throws \DOMException
     */
    protected function createXmlParam($item, $values) {
        if (!is_array($values) || count($values) <= 0) {
            return false;
        }

        foreach ($values as $value) {
            $param = $this->xml->createElement('PARAM');
            $paramName = $this->xml->createElement('NAME');
            $paramValue = $this->xml->createElement('VALUE');

            $paramName->appendChild($this->xml->createCDATASection($value['name']));
            $paramValue->appendChild($this->xml->createCDATASection($value['value']));

            $param->appendChild($paramName);
            $param->appendChild($paramValue);

            $item->appendChild($param);
        }

        return $item;
    }
}
