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

class BianoStarService
{

    use SingletonTrait;

    public const ACTIVE = 'mmp_biano_star_active';
    public const SHIPMENT_IN_STOCK = 'mmp_biano_star_shipment_in_stock';
    public const SHIPMENT_BACKORDER = 'mmp_biano_star_shipment_backorder';
    public const SHIPMENT_OUT_OF_STOCK = 'mmp_biano_star_shipment_out_of_stock';

    public const OPT_OUT = 'mmp_biano_start_opt_out_text_';

    public const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Biano Star program.';

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    /**
     * Biano star is dependant on Biano
     * Check of Biano activation is omitted because this function is used only inside Biano call.
     */
    public function isActive(string $lang): bool
    {
        $active = $this->getActive();
        $bianoService = BianoService::getInstance();

        return $active === 1 && $bianoService->isActive($lang);
    }

    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    public function getActive(): int
    {
        return (int)get_option(self::ACTIVE, 0);
    }

    public function getShipmentInStock(): int
    {
        return (int)get_option(self::SHIPMENT_IN_STOCK, 0);
    }

    public function getShipmentBackorder(): int
    {
        return (int)get_option(self::SHIPMENT_BACKORDER, 0);
    }

    public function getShipmentOutOfStock(): int
    {
        return (int)get_option(self::SHIPMENT_OUT_OF_STOCK, 0);
    }

    public function getOptOut(string $lang): string
    {
        return get_option(self::OPT_OUT . $lang, '');
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        $inputs = [];
        $checkboxes = [];

        foreach (get_available_languages() as $lang) {
            $inputs[] = self::OPT_OUT . $lang;
        }

        $inputs[] = self::OPT_OUT . 'en_US';
        $inputs[] = self::SHIPMENT_IN_STOCK;
        $inputs[] = self::SHIPMENT_BACKORDER;
        $inputs[] = self::SHIPMENT_OUT_OF_STOCK;

        $checkboxes[] = self::ACTIVE;

        DatabaseManager::saveOptions($post,
            $checkboxes
            ,
            $inputs
        );
    }
}
