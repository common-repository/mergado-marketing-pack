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

namespace Mergado\Service\External\Google\GoogleAds;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class GoogleAdsService
{
    use SingletonTrait;

    public const CONVERSION_ACTIVE = 'adwords-form-conversion-active';
    public const ENHANCED_CONVERSION_ACTIVE = 'mmp-google-gads-enhanced-conversions-active';
    public const REMARKETING_ACTIVE = 'adwords-form-remarketing-active';
    public const CONVERSION_CODE = 'adwords-form-conversion-code';
    public const CONVERSION_LABEL = 'adwords-form-conversion-label';

    public const CONVERSION_VAT_INCL = 'mmp-google-gads-vat-included';
    public const SHIPPING_PRICE_INCL = 'mmp-google-gads-shipping-included';

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isConversionActive(): bool
    {
        $active = $this->getConversionActive();
        $code = $this->getConversionCode();
        $label = $this->getConversionLabel();

        return $active === 1 && $code && $code !== '' && $label && $label !== '';
    }

    public function isRemarketingActive(): bool
    {
        $active = $this->getRemarketingActive();
        $code = $this->getConversionCode();

        return $active === 1 && $code && $code !== '';
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    public function isShippingPriceIncluded(): bool
    {
        return $this->getShippingPriceIncluded() === 1;
    }


    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getConversionActive(): int
    {
        return (int)get_option(self::CONVERSION_ACTIVE, 0);
    }

    public function getEnhancedConversionsActive(): int
    {
        return (int)get_option(self::ENHANCED_CONVERSION_ACTIVE, 0);

    }

    public function getRemarketingActive(): int
    {
        return (int)get_option(self::REMARKETING_ACTIVE, 0);

    }

    public function getConversionCode(): string
    {
        $code = get_option(self::CONVERSION_CODE, '');

        if (trim($code) !== '' && strpos($code, "AW-") !== 0) {
            return 'AW-' . $code;
        }

        return $code;
    }

    public function getConversionLabel(): string
    {
        return get_option(self::CONVERSION_LABEL, '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    public function getShippingPriceIncluded(): int
    {
        return (int)get_option(self::SHIPPING_PRICE_INCL, 0);
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            self::CONVERSION_ACTIVE,
            self::REMARKETING_ACTIVE,
            self::ENHANCED_CONVERSION_ACTIVE,
            self::CONVERSION_VAT_INCL,
            self::SHIPPING_PRICE_INCL
        ], [
            self::CONVERSION_CODE,
            self::CONVERSION_LABEL
        ]);
    }
}
