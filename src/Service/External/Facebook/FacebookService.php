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

namespace Mergado\Service\External\Facebook;

use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class FacebookService
{
    use SingletonTrait;

    public const ACTIVE = 'facebook-form-active';
    public const CODE = 'facebook-form-pixel';
    public const CONVERSION_VAT_INCL = 'facebook-vat-included';

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isActive(): bool
    {
        $active = $this->getActive();
        $code = $this->getCode();

        return $active && $code && $code !== '';
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

    public function getCode(): string
    {
        return get_option(self::CODE, '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            self::ACTIVE,
            self::CONVERSION_VAT_INCL,
        ], [
            self::CODE,
        ]);
    }
}
