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

namespace Mergado\Service\External\Google\GoogleAnalytics\Universal;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class GaUniversalService
{
    use SingletonTrait;

    public const ACTIVE = 'mmp-ga-ua-active';
    public const CODE = 'mmp-ga-ua-code';
    public const ECOMMERCE = 'mmp-ga-ua-ecommerce';
    public const ECOMMERCE_ENHANCED = 'mmp-ga-ua-enhanced-ecommerce';
    public const CONVERSION_VAT_INCL = 'mmp-ga-ua-vat-included';

    private $active;
    private $code;
    private $ecommerce;
    private $ecommerceEnhanced;
    private $conversionVatIncluded;

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

        if ($active === 1 && $code !== '') {
            return true;
        }

        return false;
    }

    public function isActiveEcommerce(): bool
    {
        $activeEcommerce = $this->getEcommerce();

        if ($activeEcommerce === 1 && $this->isActive()) {
            return true;
        }

        return false;
    }

    public function isActiveEnhancedEcommerce(): bool
    {
        $activeEnhancedEcommerce = $this->getEnhancedEcommerce();

        if ($activeEnhancedEcommerce === 1 && $this->isActiveEcommerce()) {
            return true;
        }

        return false;
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    /**
     * GET
     */

    public function getActive(): int
    {
        if (!is_null($this->active)) {
            return $this->active;
        }

        $this->active = (int)get_option(self::ACTIVE, 0);

        return $this->active;
    }

    public function getCode(): string
    {
        if (!is_null($this->code)) {
            return $this->code;
        }

        $this->code = get_option(self::CODE, '');

        return $this->code;
    }

    public function getEcommerce(): int
    {
        if (!is_null($this->ecommerce)) {
            return $this->ecommerce;
        }

        $this->ecommerce = (int)get_option(self::ECOMMERCE, 0);

        return $this->ecommerce;
    }

    public function getEnhancedEcommerce(): int
    {
        if (!is_null($this->ecommerceEnhanced)) {
            return $this->ecommerceEnhanced;
        }

        $this->ecommerceEnhanced = (int)get_option(self::ECOMMERCE_ENHANCED, 0);

        return $this->ecommerceEnhanced;
    }

    public function getConversionVatIncluded(): int
    {
        if (!is_null($this->conversionVatIncluded)) {
            return $this->conversionVatIncluded;
        }

        $this->conversionVatIncluded = (int)get_option(self::CONVERSION_VAT_INCL, 1);

        return $this->conversionVatIncluded;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            self::ACTIVE,
            self::ECOMMERCE,
            self::ECOMMERCE_ENHANCED,
            self::CONVERSION_VAT_INCL,
        ], [
            self::CODE,
        ]);
    }
}
