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

namespace Mergado\Service\External\ArukeresoFamily\Arukereso;

use Mergado\Service\External\ArukeresoFamily\AbstractArukeresoFamilyService;
use Mergado\Traits\SingletonTrait;

class ArukeresoService extends AbstractArukeresoFamilyService
{
    use SingletonTrait;

    // BASE
    public const ACTIVE = 'arukereso-active';
    public const WEB_API_KEY = 'arukereso-web-api-key';
    public const OPT_OUT = 'arukereso-verify-opt-out-text-';

    //WIDGET
    public const WIDGET_ACTIVE = 'arukereso-widget-active';
    public const WIDGET_DESKTOP_POSITION = 'arukereso-widget-desktop-position';
    public const WIDGET_MOBILE_POSITION = 'arukereso-widget-mobile-position';
    public const WIDGET_MOBILE_WIDTH = 'arukereso-widget-mobile-width';
    public const WIDGET_APPEARANCE_TYPE = 'arukereso-widget-appearance-type';

    const FRONTEND_CHECKBOX = 'mmp-arukereso-verify-checkbox';

    const SERVICE_URL_SEND = 'https://www.arukereso.hu/'; // it is used!
}

;
