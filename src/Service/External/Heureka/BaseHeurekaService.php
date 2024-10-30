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

use Mergado\Manager\DatabaseManager;

abstract class BaseHeurekaService
{
    public const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Verified by Customer program.';
    public const POSITION_LEFT = 21;
    public const POSITION_RIGHT = 22;

    /******************************************************************************************************************
     * IS
     ******************************************************************************************************************/

    public function isVerifiedActive(): bool
    {
        $active = $this->getVerifiedActive();
        $code = $this->getVerifiedCode();

        return $active === 1 && $code && $code !== '';
    }

    public function isWidgetActive(): bool
    {
        $active = $this->getWidgetActive();
        $code = $this->getWidgetId();

        return $active === 1 && $code && $code !== '';
    }

    public function isConversionActive(): bool
    {
        $active = $this->getConversionActive();
        $code = $this->getConversionCode();

        return $active === 1 && $code && $code !== '';
    }

    public function isConversionWithVat(): bool
    {
        return $this->getConversionVatIncluded() === 1;
    }

    /******************************************************************************************************************
     * GET
     ******************************************************************************************************************/

    public function getUrl(): string
    {
        return $this::HEUREKA_URL;
    }

    public function getVerifiedActive(): int
    {
        return (int)get_option($this::VERIFIED_ACTIVE, 0);
    }

    public function getVerifiedCode(): string
    {
        return get_option($this::VERIFIED_CODE, '');
    }

    public function getWidgetActive(): int
    {
        return (int)get_option($this::WIDGET_ACTIVE, 0);
    }

    public function getWidgetId(): string
    {
        return get_option($this::WIDGET_ID, '');
    }

    public function getWidgetPosition(): int
    {
        $widgetPosition = get_option($this::WIDGET_POSITION, 0);

        if ($widgetPosition === 0) {
            $widgetPosition = self::POSITION_LEFT;
        }

        return (int)$widgetPosition;
    }

    public function getWidgetTopMargin(): int
    {
        return (int)get_option($this::WIDGET_TOP_MARGIN, 60);
    }

    public function getConversionActive(): int
    {
        return (int)get_option($this::CONVERSION_ACTIVE, 0);
    }

    public function getConversionCode(): string
    {
        return get_option($this::CONVERSION_CODE, '');
    }

    public function getConversionVatIncluded(): int
    {
        return (int)get_option($this::CONVERSION_VAT_INCL, 1);
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        DatabaseManager::saveOptions($post, [
            static::VERIFIED_ACTIVE,
            static::CONVERSION_ACTIVE,
            static::WIDGET_ACTIVE,
            static::CONVERSION_VAT_INCL
        ], [
            static::VERIFIED_CODE,
            static::CONVERSION_CODE,
            static::WIDGET_ID,
            static::WIDGET_TOP_MARGIN,
        ],
            [
                static::WIDGET_POSITION
            ]);
    }
}
