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

class GlamiTopService
{
    use SingletonTrait;

    public const ACTIVE = 'glami-top-form-active';
    public const SELECTION = 'glami-selection-top';
    public const CODE = 'glami-form-top';

    public const LANGUAGES = [
        ['id_option' => 1, 'name' => 'glami.cz', 'type_code' => 'cz'],
        ['id_option' => 2, 'name' => 'glami.de', 'type_code' => 'de'],
        ['id_option' => 3, 'name' => 'glami.fr', 'type_code' => 'fr'],
        ['id_option' => 4, 'name' => 'glami.sk', 'type_code' => 'sk'],
        ['id_option' => 5, 'name' => 'glami.ro', 'type_code' => 'ro'],
        ['id_option' => 6, 'name' => 'glami.hu', 'type_code' => 'hu'],
        ['id_option' => 7, 'name' => 'glami.ru', 'type_code' => 'ru'],
        ['id_option' => 8, 'name' => 'glami.gr', 'type_code' => 'gr'],
        ['id_option' => 9, 'name' => 'glami.com.tr', 'type_code' => 'tr'],
        ['id_option' => 10, 'name' => 'glami.bg', 'type_code' => 'bg'],
        ['id_option' => 11, 'name' => 'glami.hr', 'type_code' => 'hr'],
        ['id_option' => 12, 'name' => 'glami.si', 'type_code' => 'si'],
        ['id_option' => 13, 'name' => 'glami.es', 'type_code' => 'es'],
        ['id_option' => 14, 'name' => 'glami.com.br', 'type_code' => 'br'],
        ['id_option' => 15, 'name' => 'glami.eco', 'type_code' => 'eco'],
    ];

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $active = $this->getActive();
        $code = $this->getCode();

        return $active === 1 && $code && $code !== '';
    }

    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    public function getActive(): int
    {
        return (int)get_option(self::ACTIVE, 0);
    }

    public function getSelection(): array
    {
        $activeLangId = get_option(self::SELECTION);

        foreach (self::LANGUAGES as $item) {
            if ($item['id_option'] === (int)$activeLangId) {
                $this->selection = $item;
                return $this->selection;
            }
        }

        return [];
    }

    public function getCode(): string
    {
        return get_option(self::CODE, '');
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post,
            [
                self::ACTIVE,
            ], [
                self::CODE,
                self::SELECTION,
            ]
        );
    }
}
