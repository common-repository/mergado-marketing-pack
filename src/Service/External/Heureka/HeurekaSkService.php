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

class HeurekaSkService extends BaseHeurekaService
{
    use SingletonTrait;

    // Verified
    public const VERIFIED_ACTIVE = 'heureka-verified-sk-form-active';
    public const VERIFIED_CODE = 'heureka-verified-sk-form-code';

    // Verified - WIDGET
    public const WIDGET_ACTIVE = 'heureka-widget-sk-form-active';
    public const WIDGET_ID = 'heureka-widget-sk-id';
    public const WIDGET_POSITION = 'heureka-widget-sk-position';
    public const WIDGET_TOP_MARGIN = 'heureka-widget-sk-margin';

    // Tracking
    public const CONVERSION_ACTIVE = 'heureka-track-sk-form-active';
    public const CONVERSION_CODE = 'heureka-track-sk-form-code';
    public const CONVERSION_VAT_INCL = 'heureka-vat-sk-included';

    // Endpoints
    public const HEUREKA_URL = 'https://www.heureka.sk/direct/dotaznik/objednavka.php';
}
