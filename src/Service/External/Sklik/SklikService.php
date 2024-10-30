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

namespace Mergado\Service\External\Sklik;

use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class SklikService
{
    use SingletonTrait;

    // Conversions
    public const CONVERSION_ACTIVE = 'sklik-form-conversion-active';
    public const CONVERSION_CODE = 'sklik-form-conversion-code';
    public const CONVERSION_VALUE = 'sklik-form-conversion-value';
    public const CONVERSION_VAT_INCL = 'sklik-vat-included';

    // Retargeting
    public const RETARGETING_ACTIVE = 'sklik-form-retargeting-active';
    public const RETARGETING_ID = 'sklik-form-retargeting-id';

    /*******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isConversionActive(): bool
    {
        $active = $this->getConversionActive();
        $id = $this->getConversionCode();

        return $active === 1 && $id && $id !== '';
    }

    public function isRetargetingActive(): bool
    {
        $active = $this->getRetargetingActive();
        $id = $this->getRetargetingId();

        return $active === 1 && $id && $id !== '';
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getConversionActive(): int
    {
        return (int)get_option(self::CONVERSION_ACTIVE, 0);
    }

    public function getConversionCode(): string
    {
        return get_option(self::CONVERSION_CODE, '');
    }

    public function getConversionValue(): string
    {
        return get_option(self::CONVERSION_VALUE, '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    public function getRetargetingActive(): int
    {
        return (int)get_option(self::RETARGETING_ACTIVE, 0);
    }

    public function getRetargetingId(): string
    {
        return get_option(self::RETARGETING_ID, '');
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    /**
     * @param $post
     */
    public static function saveFields($post)
    {
        DatabaseManager::saveOptions($post, [
            self::CONVERSION_ACTIVE,
            self::RETARGETING_ACTIVE,
            self::CONVERSION_VAT_INCL,
        ], [
            self::CONVERSION_CODE,
            self::CONVERSION_VALUE,
            self::RETARGETING_ID,
        ]);
    }
}

;
