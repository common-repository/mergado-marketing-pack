<?php declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Service\External\Google\GoogleAnalytics\GA4;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class Ga4Service
{
    use SingletonTrait;

    public const ACTIVE = 'mmp-google-ga4-active';
    public const CODE = 'mmp-google-ga4-code';
    public const ECOMMERCE = 'mmp-google-ga4-ecommerce';
    public const CONVERSION_VAT_INCL = 'mmp-google-ga4-vat-included';
    public const SHIPPING_PRICE_INCL = 'mmp-google-ga4-shipping-included';
    public const REFUND_STATUS = 'mmp-ga4-refund-status';

    public const REFUND_OBJECT = 'mmp-ga4-refund-items-object'; // Saves refund data and shows theme on next page reload which happens immediately after refund
    public const REFUND_PREFIX_ORDER_FULLY_REFUNDED = 'mmp-ga4-orderFullyRefunded-';

    /**
     * IS
     */

    /**
     * @return bool
     */

    public function isActive(): bool
    {
        $active = $this->getActive();
        $code = $this->getCode();

        return $active === 1 && $code !== '';
    }

    public function isActiveEcommerce(): bool
    {
        $activeEcommerce = $this->getEcommerce();

        return $activeEcommerce === 1 && $this->isActive();
    }

    public function isRefundStatusActive(string $statusKey): bool
    {
        $active = $this->getRefundStatus($statusKey);

        return $active === 1;
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    /****************************************************
     * GET
     ****************************************************/

    public function getRefundStatus(string $statusKey): int
    {
        // Default set to true
        if ($statusKey === 'wc-refunded') {
            $result = get_option(self::REFUND_STATUS . $statusKey, 1);
        } else {
            $result = get_option(self::REFUND_STATUS . $statusKey, 0);
        }

        return (int)$result;
    }

    public function getActive(): int
    {
        return (int)get_option(self::ACTIVE, 0);
    }

    public function getCode(): string
    {
        return get_option(self::CODE, '');
    }

    public function getEcommerce(): int
    {
        return (int)get_option(self::ECOMMERCE, 0);
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 1);
    }

    public function getShippingPriceIncluded(): int
    {
        return (int)get_option(self::SHIPPING_PRICE_INCL, 0);
    }

    public function getRefundObject()
    {
        return get_option(self::REFUND_OBJECT, false);
    }

    public function setRefundObject($object): bool
    {
        return update_option(self::REFUND_OBJECT, $object, true);
    }

    public function deleteRefundObject(): bool
    {
        return delete_option(self::REFUND_OBJECT);
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields($post): void
    {
        $checkboxes = array(self::ACTIVE, self::ECOMMERCE, self::CONVERSION_VAT_INCL, self::SHIPPING_PRICE_INCL);

        foreach (wc_get_order_statuses() as $key => $data) {
            $checkboxes[] = self::REFUND_STATUS . $key;
        }

        DatabaseManager::saveOptions($post, $checkboxes, [
            self::CODE,
        ]);
    }
}
