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

namespace Mergado\Service\External\ArukeresoFamily\Pazaruvaj;

use Mergado\Service\External\ArukeresoFamily\AbstractArukeresoFamilyService;
use Mergado\Traits\SingletonTrait;

class PazaruvajService extends AbstractArukeresoFamilyService
{
    use SingletonTrait;

    // BASE
    public const ACTIVE = 'mmp-pazaruvaj-active';
    public const WEB_API_KEY = 'mmp-pazaruvaj-web-api-key';
    public const OPT_OUT = 'mmp-pazaruvaj-verify-opt-out-text-';

    //WIDGET
    public const WIDGET_ACTIVE = 'mmp-pazaruvaj-widget-active';
    public const WIDGET_DESKTOP_POSITION = 'mmp-pazaruvaj-widget-desktop-position';
    public const WIDGET_MOBILE_POSITION = 'mmp-pazaruvaj-widget-mobile-position';
    public const WIDGET_MOBILE_WIDTH = 'mmp-pazaruvaj-widget-mobile-width';
    public const WIDGET_APPEARANCE_TYPE = 'mmp-pazaruvaj-widget-appearance-type';

    const FRONTEND_CHECKBOX = 'mmp-pazaruvaj-verify-checkbox'; // used in abstract integration

    const SERVICE_URL_SEND = 'https://www.pazaruvaj.com/'; // it is used!
}

;
