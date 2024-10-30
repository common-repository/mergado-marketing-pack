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

namespace Mergado\Service\External\Etarget;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class EtargetService
{
    use SingletonTrait;

    public const ACTIVE = 'etarget-form-active';
    public const HASH = 'etarget-form-hash';
    public const ID = 'etarget-form-id';

    /*******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isActive(): bool
    {
        $active = $this->getActive();
        $id = $this->getId();
        $hash = $this->getHash();

        return $active === 1 && $id && $hash && $id !== '' && $hash !== '';
    }

    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getActive(): int
    {
        return (int)get_option(self::ACTIVE, 0);
    }

    public function getId(): string
    {
        return get_option(self::ID, '');
    }

    public function getHash(): string
    {
        return get_option(self::HASH, '');
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            self::ACTIVE,
        ], [
            self::ID,
            self::HASH,
        ]);
    }
}
