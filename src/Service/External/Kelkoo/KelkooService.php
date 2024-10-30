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

namespace Mergado\Service\External\Kelkoo;

use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class KelkooService
{
    use SingletonTrait;

    public const ACTIVE = 'kelkoo_active';
    public const ID = 'kelkoo_merchant_id';
    public const COUNTRY = 'kelkoo_country';
    public const CONVERSION_VAT_INCL = 'kelkoo-vat-included';

    public const COUNTRIES = [
        ['id_option' => 1, 'name' => 'Austria', 'type_code' => 'at'],
        ['id_option' => 2, 'name' => 'Belgium', 'type_code' => 'be'],
        ['id_option' => 3, 'name' => 'Brazil', 'type_code' => 'br'],
        ['id_option' => 4, 'name' => 'Switzerland', 'type_code' => 'ch'],
        ['id_option' => 5, 'name' => 'Czech Republic', 'type_code' => 'cz'],
        ['id_option' => 6, 'name' => 'Germany', 'type_code' => 'de'],
        ['id_option' => 7, 'name' => 'Denmark', 'type_code' => 'dk'],
        ['id_option' => 8, 'name' => 'Spain', 'type_code' => 'es'],
        ['id_option' => 9, 'name' => 'Finland', 'type_code' => 'fi'],
        ['id_option' => 10, 'name' => 'France', 'type_code' => 'fr'],
        ['id_option' => 11, 'name' => 'Ireland', 'type_code' => 'ie'],
        ['id_option' => 12, 'name' => 'Italy', 'type_code' => 'it'],
        ['id_option' => 13, 'name' => 'Mexico', 'type_code' => 'mx'],
        ['id_option' => 14, 'name' => 'Flemish Belgium', 'type_code' => 'nb'],
        ['id_option' => 15, 'name' => 'Netherlands', 'type_code' => 'nl'],
        ['id_option' => 16, 'name' => 'Norway', 'type_code' => 'no'],
        ['id_option' => 17, 'name' => 'Poland', 'type_code' => 'pl'],
        ['id_option' => 18, 'name' => 'Portugal', 'type_code' => 'pt'],
        ['id_option' => 19, 'name' => 'Russia', 'type_code' => 'ru'],
        ['id_option' => 20, 'name' => 'Sweden', 'type_code' => 'se'],
        ['id_option' => 21, 'name' => 'United Kingdom', 'type_code' => 'uk'],
        ['id_option' => 22, 'name' => 'United States', 'type_code' => 'us'],
    ];

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isActive(): bool
    {
        $active = $this->getActive();
        $code = $this->getId();
        $activeDomain = $this->getCountryActiveDomain();

        return $active === 1 && $code && $code !== '' && $activeDomain && $activeDomain !== '';
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

    public function getCountry(): array
    {
        $activeLangId = get_option(self::COUNTRY);

        foreach (self::COUNTRIES as $item) {
            if ($item['id_option'] === (int)$activeLangId) {
                return $item;
            }
        }

        return [];
    }

    public function getId(): string
    {
        return get_option(self::ID, '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    /**
     * Return active language options for Kelkoo
     */
    public function getCountryActiveDomain()
    {
        $country = $this->getCountry();

        if ($country) {
            return $country['type_code'];
        }

        return false;
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post,
            [
                self::ACTIVE,
                self::CONVERSION_VAT_INCL,
            ], [
                self::ID,
                self::COUNTRY,
            ]
        );
    }
}
