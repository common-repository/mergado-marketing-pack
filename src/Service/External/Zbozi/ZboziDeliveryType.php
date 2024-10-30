<?php

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

class ZboziDeliveryType
{
    public static function getDeliveryType($type)
    {
        // This list is taken from official documentation:
        // https://napoveda.zbozi.cz/xml-feed/specifikace/#DELIVERY

        switch($type) {
            case 'AlzaBox':
                return 'ALZABOX';
                break;
            case 'Balíkovna':
                return 'CESKA_POSTA_BALIKOVNA';
                break;
            case 'Česká pošta na poštu': // Modified because of duplicate
                return 'CESKA_POSTA_NA_POSTU';
                break;
            case 'DPD Pickup':
                return 'DPD_PICKUP';
                break;
            case 'GLS Parcelshop':
                return 'GLS_PARCELSHOP';
                break;
            case 'PPL ParcelShop':
                return 'PPL_PARCELSHOP';
                break;
            case 'Toptrans Depo':
                return 'TOPTRANS_DEPO';
                break;
            case 'WE|DO Point':
                return 'WEDO_POINT';
                break;
            case 'Zásilkovna':
                return 'ZASILKOVNA';
                break;
            case 'Vlastní výdejní místa':
            case 'Vlastní místa':
            case 'Místní vyzvednutí':
                return 'VLASTNI_VYDEJNI_MISTA';
                break;
            case '123 kurýr':
                return '123_KURYR';
                break;
            case 'Česká pošta':
                return 'CESKA_POSTA';
                break;
            case 'Balíkovna na adresu':
                return 'BALIKOVNA_NA_ADRESU';
                break;
            case 'DB_SCHENKER':
                return 'DB_SCHENKER';
                break;
            case 'DPD':
                return 'DPD';
                break;
            case 'DHL':
                return 'DHL';
                break;
            case 'DSV':
                return 'DSV';
                break;
            case 'FOFR':
                return 'FOFR';
                break;
            case 'Gebrüder Weiss':
                return 'GEBRUDER_WEISS';
                break;
            case 'Geis':
                return 'GEIS';
                break;
            case 'GLS':
                return 'GLS';
                break;
            case 'HDS':
                return 'HDS';
                break;
            case 'WE|DO HOME':
                return 'WE|DO HOME';
                break;
            case 'Náš kurýr':
                return 'NAS_KURYR';
                break;
            case 'MESSENGER':
                return 'MESSENGER';
                break;
            case 'PPL':
                return 'PPL';
                break;
            case 'TNT':
                return 'TNT';
                break;
            case 'TOPTRANS':
                return 'TOPTRANS';
                break;
            case 'UPS':
                return 'UPS';
                break;
            case 'FedEx':
                return 'FEDEX';
                break;
            case 'Raben Logistics':
                return 'RABEN_LOGISTICS';
                break;
            case 'RHENUS':
                return 'RHENUS';
                break;
            case 'Zásilkovna (na adresu)':
                return 'ZASILKOVNA_NA_ADRESU';
                break;
            case 'Vlastní přeprava':
                return 'VLASTNI_PREPRAVA';
                break;
            default:
                return $type;
                break;
        }
    }
}
