<?php

namespace Mergado\Feed\Product;

use DOMException;
use Exception;
use Mergado\Feed\Shared\AbstractFeedItem;
use Mergado\Service\LogService;

class ProductFeedItem extends AbstractFeedItem
{
    // XML PROPERTIES

    public $itemId;
    public $availability;
    public $url;
    public $nameExact; //CDATA
    public $currency;
    public $vat;
    public $price;
    public $priceVat;
    public $priceDiscount;
    public $priceDiscountVat;
    public $salePriceEffectiveDate;
    public $image;
    public $imageAlternative;
    public $param;
    public $stockQuantity;
    public $description;
    public $descriptionShort;
    public $category;
    public $tags = [];
    public $ean;
    public $shippingSize;
    public $shippingWeight;
    public $itemgroupId;
    public $productno;
    public $variantDescription;
    public $customProperties = null;
    public $visibility;
    public $catalogVisibility;

    private $productType;
    private $logger;

    public function __construct($productType)
    {
        $this->logger = LogService::getInstance();
        $this->productType = $productType;

        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function __set($name, $value) {
        throw new Exception("Cannot add new property \$$name to instance of " . __CLASS__ . '. Use set methods instead!');
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId): void
    {
        $this->itemId = $itemId;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param mixed $availability
     */
    public function setAvailability($availability): void
    {
        $this->availability = $availability;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getNameExact()
    {
        return $this->nameExact;
    }

    /**
     * @param mixed $nameExact
     */
    public function setNameExact($nameExact): void
    {
        $this->nameExact = $nameExact;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param mixed $vat
     */
    public function setVat($vat): void
    {
        $this->vat = $vat;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPriceVat()
    {
        return $this->priceVat;
    }

    /**
     * @param mixed $priceVat
     */
    public function setPriceVat($priceVat): void
    {
        $this->priceVat = $priceVat;
    }

    /**
     * @return mixed
     */
    public function getPriceDiscount()
    {
        return $this->priceDiscount;
    }

    /**
     * @param mixed $priceDiscount
     */
    public function setPriceDiscount($priceDiscount): void
    {
        $this->priceDiscount = $priceDiscount;
    }

    /**
     * @return mixed
     */
    public function getPriceDiscountVat()
    {
        return $this->priceDiscountVat;
    }

    /**
     * @param mixed $priceDiscountVat
     */
    public function setPriceDiscountVat($priceDiscountVat): void
    {
        $this->priceDiscountVat = $priceDiscountVat;
    }

    /**
     * @return mixed
     */
    public function getSalePriceEffectiveDate()
    {
        return $this->salePriceEffectiveDate;
    }

    /**
     * @param mixed $salePriceEffectiveDate
     */
    public function setSalePriceEffectiveDate($salePriceEffectiveDate): void
    {
        $this->salePriceEffectiveDate = $salePriceEffectiveDate;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return array|null
     */
    public function getImageAlternative(): ?array
    {
        return $this->imageAlternative;
    }

    /**
     * @param array|null $imageAlternative
     */
    public function setImageAlternative(?array $imageAlternative): void
    {
        $this->imageAlternative = $imageAlternative;
    }

    /**
     * @return mixed
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param mixed $param
     */
    public function setParam($param): void
    {
        $this->param = $param;
    }

    /**
     * @return mixed
     */
    public function getStockQuantity()
    {
        return $this->stockQuantity;
    }

    /**
     * @param mixed $stockQuantity
     */
    public function setStockQuantity($stockQuantity): void
    {
        $this->stockQuantity = $stockQuantity;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescriptionShort()
    {
        return $this->descriptionShort;
    }

    /**
     * @param mixed $descriptionShort
     */
    public function setDescriptionShort($descriptionShort): void
    {
        $this->descriptionShort = $descriptionShort;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param mixed $ean
     */
    public function setEan($ean): void
    {
        $this->ean = $ean;
    }

    /**
     * @return mixed
     */
    public function getShippingSize()
    {
        return $this->shippingSize;
    }

    /**
     * @param mixed $shippingSize
     */
    public function setShippingSize($shippingSize): void
    {
        $this->shippingSize = $shippingSize;
    }

    /**
     * @return mixed
     */
    public function getShippingWeight()
    {
        return $this->shippingWeight;
    }

    /**
     * @param mixed $shippingWeight
     */
    public function setShippingWeight($shippingWeight): void
    {
        $this->shippingWeight = $shippingWeight;
    }

    /**
     * @return mixed
     */
    public function getItemgroupId()
    {
        return $this->itemgroupId;
    }

    /**
     * @param mixed $itemgroupId
     */
    public function setItemgroupId($itemgroupId): void
    {
        $this->itemgroupId = $itemgroupId;
    }

    /**
     * @return mixed
     */
    public function getProductno()
    {
        return $this->productno;
    }

    /**
     * @param mixed $productno
     */
    public function setProductno($productno): void
    {
        $this->productno = $productno;
    }

    /**
     * @return mixed
     */
    public function getVariantDescription()
    {
        return $this->variantDescription;
    }

    /**
     * @param mixed $variantDescription
     */
    public function setVariantDescription($variantDescription): void
    {
        $this->variantDescription = $variantDescription;
    }

    /**
     * @return mixed
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param mixed $visibility
     */
    public function setVisibility($visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return mixed
     */
    public function getCatalogVisibility()
    {
        return $this->catalogVisibility;
    }

    /**
     * @param mixed $catalogVisibility
     */
    public function setCatalogVisibility($catalogVisibility): void
    {
        $this->catalogVisibility = $catalogVisibility;
    }

    /**
     * @return mixed
     */
    public function getCustomProperties()
    {
        return $this->customProperties;
    }

    /**
     * @param array $customProperties
     * @return void
     */
    public function setCustomProperties(array $customProperties): void
    {
        $this->customProperties = $customProperties;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getItemXml() {
        try {
            $item = $this->xml->createElement('ITEM');

            $this->createXmlItemProperty($item, 'ITEM_ID', $this->getItemId());
            $this->createXmlItemProperty($item, 'AVAILABILITY', $this->getAvailability());
            $this->createXmlItemProperty($item, 'URL', $this->getUrl());
            $this->createXmlItemProperty($item, 'NAME_EXACT', $this->getNameExact(), true);
            $this->createXmlItemProperty($item, 'CURRENCY', $this->getCurrency());
            $this->createXmlItemProperty($item, 'VAT', $this->getVat());
            $this->createXmlItemProperty($item, 'PRICE', $this->getPrice());
            $this->createXmlItemProperty($item, 'PRICE_VAT', $this->getPriceVat());
            $this->createXmlItemProperty($item, 'PRICE_DISCOUNT', $this->getPriceDiscount());
            $this->createXmlItemProperty($item, 'PRICE_DISCOUNT_VAT', $this->getPriceDiscountVat());
            $this->createXmlItemProperty($item, 'SALE_PRICE_EFFECTIVE_DATE', $this->getSalePriceEffectiveDate());
            $this->createXmlItemProperty($item, 'IMAGE', $this->getImage());
            $this->createXmlItemPropertyArray($item, 'IMAGE_ALTERNATIVE', $this->getImageAlternative());
            $this->createXmlParam($item, $this->getParam());
            $this->createXmlItemProperty($item, 'STOCK_QUANTITY', $this->getStockQuantity());
            $this->createXmlItemProperty($item, 'DESCRIPTION', $this->getDescription(), true, true);
            $this->createXmlItemProperty($item, 'DESCRIPTION_SHORT', $this->getDescriptionShort(), true, true);
            $this->createXmlItemProperty($item, 'CATEGORY', $this->getCategory(), true);
            $this->createXmlItemProperty($item, 'EAN', $this->getEan());
            $this->createXmlItemProperty($item, 'SHIPPING_SIZE', $this->getShippingSize());
            $this->createXmlItemProperty($item, 'SHIPPING_WEIGHT', $this->getShippingWeight());
            $this->createXmlItemProperty($item, 'ITEMGROUP_ID', $this->getItemgroupId());
            $this->createXmlItemProperty($item, 'PRODUCTNO', $this->getProductno(), true);
            $this->createXmlItemProperty($item, 'VISIBILITY', $this->getVisibility());
            $this->createXmlItemProperty($item, 'CATALOG_VISIBILITY', $this->getCatalogVisibility());

            if (ProductFeed::is_product_type($this->productType, 'variation')) {
                $this->createXmlItemProperty($item, 'VARIANT_DESCRIPTION', $this->getVariantDescription(), true, true);
            }

            foreach($this->getTags() as $tagName) {
                $this->createXmlItemProperty($item, 'TAG', $tagName, true);
            }

            if ($this->getCustomProperties() !== null) {
                foreach($this->getCustomProperties() as $customPropertyKey => $customPropertyValue) {
                    $this->createCustomProperties($item, $customPropertyKey, $customPropertyValue);
                }
            }

            $this->xml->appendChild($item);

            return $this->xml;
        } catch (DOMException $e) {
            $this->logger->error('Error creating ProductFeedItem of product ID: ' . $this->getItemId(), ProductFeed::getLogContext());
        }

        return false;
    }
}
