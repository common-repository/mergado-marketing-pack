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

namespace Mergado\Service\External\Biano;

use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class BianoService
{
    use SingletonTrait;

    public const ACTIVE = 'biano_active';
    public const MERCHANT_ID = 'biano_merchant_id';
    public const ACTIVE_LANG = 'biano-form-active-lang';
    public const LANGUAGES = ['CZ', 'SK', 'RO', 'NL', 'HU'];
    public const CONVERSION_VAT_INCL = 'biano-vat-included';

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isActive($lang): bool
    {
        $active = $this->getActive();
        $merchantId = $this->getMerchantId($lang);
        $activeLanguage = $this->getActiveLang($lang);

        return $active === 1 && $merchantId && $merchantId !== '' && $activeLanguage === 1;
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    public function getActive(): int
    {
        return (int)get_option(self::ACTIVE, 0);
    }

    public function getActiveLang(string $lang): int
    {
        if ('' === trim($lang)) {
            return 0;
        }

        return (int)get_option(self::getActiveLangName($lang), 0);
    }

    public function getMerchantId(string $lang): string
    {
        if ('' === trim($lang)) {
            return '';
        }

        return get_option(self::getMerchantIdName($lang), '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    /*******************************************************************************************************************
     * GET NAMES
     ******************************************************************************************************************/

    public static function getMerchantIdName(string $lang): string
    {
        return self::MERCHANT_ID . '-' . $lang;
    }

    public static function getActiveLangName(string $lang): string
    {
        return self::ACTIVE_LANG . '-' . $lang;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        $inputs = [];
        $checkboxes = [];

        foreach (self::LANGUAGES as $key => $item) {
            $inputs[] = self::getMerchantIdName($item);
            $checkboxes[] = self::getActiveLangName($item);
        }

        $checkboxes[] = self::ACTIVE;
        $checkboxes[] = self::CONVERSION_VAT_INCL;

        DatabaseManager::saveOptions($post,
            $checkboxes
            ,
            $inputs
        );
    }
}
