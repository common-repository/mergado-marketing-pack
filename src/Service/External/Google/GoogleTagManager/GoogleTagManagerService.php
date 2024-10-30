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

namespace Mergado\Service\External\Google\GoogleTagManager;

use Mergado;
use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class GoogleTagManagerService
{
    use SingletonTrait;

    public const ACTIVE = 'mergado_google_tag_manager_active';
    public const CODE = 'mergado_google_tag_manager_code';
    public const ECOMMERCE_ACTIVE = 'mergado_google_tag_manager_ecommerce';
    public const ECOMMERCE_ENHANCED_ACTIVE = 'mergado_google_tag_manager_ecommerce_enhanced';
    public const CONVERSION_VAT_INCL = 'gtm-vat-included';
    public const VIEW_LIST_ITEMS_COUNT = 'mergado_google_tag_manager_view_list_items_count';

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isActive(): bool
    {
        $active = $this->getActive();
        $code = $this->getCode();

        return $active === 1 && $code && $code !== '';
    }

    public function isEcommerceActive(): bool
    {
        $ecommerceActive = $this->getEcommerceActive();

        return $ecommerceActive === 1 && $this->isActive();
    }

    public function isEnhancedEcommerceActive(): bool
    {
        $enhancedEcommerceActive = $this->getEnhancedEcommerceActive();

        return $enhancedEcommerceActive === 1 && $this->isEcommerceActive();
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
        $code = get_option(self::CODE, '');

        if (trim($code) !== '' && strpos($code, "GTM-") !== 0) {
            return 'GTM-' . $code;
        }

        return $code;
    }

    public function getEcommerceActive(): int
    {
        return (int)get_option(self::ECOMMERCE_ACTIVE, 0);
    }

    public function getEnhancedEcommerceActive(): int
    {
        return (int)get_option(self::ECOMMERCE_ENHANCED_ACTIVE, 0);
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option(self::CONVERSION_VAT_INCL, 0);
    }

    public function getViewListItemsCount(): int
    {
        return (int)get_option(self::VIEW_LIST_ITEMS_COUNT, 0);
    }


    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            self::ACTIVE,
            self::ECOMMERCE_ACTIVE,
            self::ECOMMERCE_ENHANCED_ACTIVE,
            self::CONVERSION_VAT_INCL,
        ], [
            self::CODE,
            self::VIEW_LIST_ITEMS_COUNT,
        ]);
    }
}
