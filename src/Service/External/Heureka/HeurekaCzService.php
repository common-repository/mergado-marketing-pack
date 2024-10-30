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

namespace Mergado\Service\External\Heureka;

use Mergado\Traits\SingletonTrait;

class HeurekaCzService extends BaseHeurekaService
{
    use SingletonTrait;

    // Verified
    public const VERIFIED_ACTIVE = 'heureka-verified-cz-form-active';
    public const VERIFIED_CODE = 'heureka-verified-cz-form-code';

    // Verified - WIDGET
    public const WIDGET_ACTIVE = 'heureka-widget-cz-form-active';
    public const WIDGET_ID = 'heureka-widget-cz-id';
    public const WIDGET_POSITION = 'heureka-widget-cz-position';
    public const WIDGET_TOP_MARGIN = 'heureka-widget-cz-margin';

    // Tracking
    public const CONVERSION_ACTIVE = 'heureka-track-cz-form-active';
    public const CONVERSION_CODE = 'heureka-track-cz-form-code';
    public const CONVERSION_VAT_INCL = 'heureka-vat-cz-included';

    // Endpoints
    public const HEUREKA_URL = 'https://www.heureka.cz/direct/dotaznik/objednavka.php';
}
