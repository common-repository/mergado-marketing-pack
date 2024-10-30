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

namespace Mergado\Service\External\Glami;

use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class GlamiPixelService
{

    use SingletonTrait;

    public const ACTIVE = 'glami-form-active';
    public const ACTIVE_LANG = 'glami-form-active-lang';
    public const CODE = 'glami-form-pixel';
    public const CONVERSION_VAT_INCL = 'glami-vat-included';
    public const LANGUAGES = ['CZ', 'DE', 'SK', 'RO', 'HU', 'RU', 'GR', 'TR', 'BG', 'HR', 'SI', 'ES', 'BR', 'ECO'];

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isActive(string $lang): bool
    {
        $active = $this->getActive();
        $code = $this->getCode($lang);
        $activeLanguage = $this->getActiveLang($lang);

        return $active === 1 && $code && $code !== '' && $activeLanguage === 1;
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

    public function getCode(string $lang): string
    {
        if ('' === trim($lang)) {
            return '';
        }

        return get_option(self::getCodeName($lang), '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    /*******************************************************************************************************************
     * GET NAMES
     ******************************************************************************************************************/

    public static function getCodeName(string $lang): string
    {
        return self::CODE . '-' . $lang;
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
            $inputs[] = self::getCodeName($item);
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
