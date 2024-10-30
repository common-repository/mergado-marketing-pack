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

namespace Mergado\Service\External\ArukeresoFamily\Compari;

use Mergado\Service\External\ArukeresoFamily\AbstractArukeresoFamilyService;
use Mergado\Traits\SingletonTrait;

class CompariService extends AbstractArukeresoFamilyService
{
    use SingletonTrait;

    // BASE
    public const ACTIVE = 'mmp-compari-active';
    public const WEB_API_KEY = 'mmp-compari-web-api-key';
    public const OPT_OUT = 'mmp-compari-verify-opt-out-text-';

    //WIDGET
    public const WIDGET_ACTIVE = 'mmp-compari-widget-active';
    public const WIDGET_DESKTOP_POSITION = 'mmp-compari-widget-desktop-position';
    public const WIDGET_MOBILE_POSITION = 'mmp-compari-widget-mobile-position';
    public const WIDGET_MOBILE_WIDTH = 'mmp-compari-widget-mobile-width';
    public const WIDGET_APPEARANCE_TYPE = 'mmp-compari-widget-appearance-type';

    const FRONTEND_CHECKBOX = 'mmp-compari-verify-checkbox';

    const SERVICE_URL_SEND = 'https://www.compari.ro/'; // it is used!
}

;
