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

namespace Mergado\Service\External\Zbozi;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class ZboziService
{
    use SingletonTrait;

    public const ZBOZI_SANDBOX = false;

    public const ACTIVE = 'zbozi-form-active';
    public const STANDARD_ACTIVE = 'zbozi-form-standard-active';
    public const ID = 'zbozi-form-id';
    public const KEY = 'zbozi-form-secret-key';
    public const CONVERSION_VAT_INCL = 'zbozi-vat-included';
    public const OPT_IN = 'zbozi-verify-opt-in-text-';

    public const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Zboží.cz program.';

    /*******************************************************************************************************************
     *******************************************************************************************************************
     ********************************************** DEFAULT CLASS OPTIONS
     ******************************************************************************************************************
     ******************************************************************************************************************/

    public function isActive(): bool
    {
        $active = $this->getActive();
        $id = $this->getId();
        $key = $this->getKey();

        return $active === 1 && $id && $key && $id !== '' && $key !== '';
    }

    public function isAdvanced(): bool
    {
        $active = $this->getStandardActive();

        return $active === 1;
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getActive(): int
    {
        return (int)get_option(self::ACTIVE, 0);
    }

    public function getStandardActive(): int
    {
        return (int)get_option(self::STANDARD_ACTIVE, 0);
    }

    public function getId(): string
    {
        return get_option(self::ID, '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 1);
    }

    public function getKey(): string
    {
        return get_option(self::KEY, '');
    }

    public function getOptOut(string $lang): string
    {
        return get_option(self::OPT_IN . $lang, '');
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        foreach (get_available_languages() as $lang) {
            $inputs[] = self::OPT_IN . $lang;
        }

        $inputs[] = self::OPT_IN . 'en_US';

        $inputs[] = self::ID;
        $inputs[] = self::KEY;

        DatabaseManager::saveOptions($post, [
            self::ACTIVE,
            self::STANDARD_ACTIVE,
            self::CONVERSION_VAT_INCL,
        ], $inputs);
    }
}

;
