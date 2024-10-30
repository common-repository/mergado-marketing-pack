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

namespace Mergado\Service\External\ArukeresoFamily;

use Mergado;
use Mergado\Manager\DatabaseManager;

abstract class AbstractArukeresoFamilyService
{
    public const DEFAULT_OPT = 'Do not send a satisfaction questionnaire within the Trusted Shop program.';

    /*******************************************************************************************************************
     *******************************************************************************************************************
     ********************************************** DEFAULT CLASS OPTIONS
     ******************************************************************************************************************
     ******************************************************************************************************************/

    public function isActive(): bool
    {
        $active = $this->getActive();
        $webApiKey = $this->getWebApiKey();

        return $active === 1 && $webApiKey && $webApiKey !== '';
    }

    public function isWidgetActive(): bool
    {
        $active = $this->getActive();
        $activeWidget = $this->getWidgetActive();
        $webApiKey = $this->getWebApiKey();

        return $active === 1 && $activeWidget === 1 && $webApiKey && $webApiKey !== '';
    }

    /*******************************************************************************************************************
     * Get constants that need to be translated
     *******************************************************************************************************************/

    public static function getMobilePositionsConstant(): array
    {
        return [
            0 => ['id_option' => 0, 'name' => __('On the left side', 'mergado-marketing-pack'), 'value' => 'L', 'mergado-marketing-pack'],
            1 => ['id_option' => 1, 'name' => __('On the right side', 'mergado-marketing-pack'), 'value' => 'R', 'mergado-marketing-pack'],
            2 => ['id_option' => 2, 'name' => __('At the left bottom of the window', 'mergado-marketing-pack'), 'value' => 'BL', 'mergado-marketing-pack'],
            3 => ['id_option' => 3, 'name' => __('At the right bottom of the window', 'mergado-marketing-pack'), 'value' => 'BR', 'mergado-marketing-pack'],
            4 => ['id_option' => 4, 'name' => __('Wide button at the bottom of the page', 'mergado-marketing-pack'), 'value' => 'W', 'mergado-marketing-pack'],
            5 => ['id_option' => 5, 'name' => __('On the left, only the badge is visible', 'mergado-marketing-pack'), 'value' => 'LB', 'mergado-marketing-pack'],
            6 => ['id_option' => 6, 'name' => __('On the left, only the text is visible', 'mergado-marketing-pack'), 'value' => 'LT', 'mergado-marketing-pack'],
            7 => ['id_option' => 7, 'name' => __('On the right, only badge is visible', 'mergado-marketing-pack'), 'value' => 'RB', 'mergado-marketing-pack'],
            8 => ['id_option' => 8, 'name' => __('On the right, only the text is visible', 'mergado-marketing-pack'), 'value' => 'RT', 'mergado-marketing-pack'],
            9 => ['id_option' => 9, 'name' => __('At the left bottom of the window, only the badge is visible', 'mergado-marketing-pack'), 'value' => 'BLB', 'mergado-marketing-pack'],
            10 => ['id_option' => 10, 'name' => __('At the left bottom of the window, only the text is visible', 'mergado-marketing-pack'), 'value' => 'BLT', 'mergado-marketing-pack'],
            11 => ['id_option' => 11, 'name' => __('At the right bottom of the window, only the badge is visible', 'mergado-marketing-pack'), 'value' => 'BRB', 'mergado-marketing-pack'],
            12 => ['id_option' => 12, 'name' => __('At the right bottom of the window, only the text is visible', 'mergado-marketing-pack'), 'value' => 'BRT', 'mergado-marketing-pack'],
            13 => ['id_option' => 13, 'name' => __('Don\'t show on mobile devices', 'mergado-marketing-pack'), 'value' => '', 'mergado-marketing-pack'],
        ];
    }

    public static function DESKTOP_POSITIONS(): array
    {
        return [
            0 => ['id_option' => 0, 'name' => __('Left', 'mergado-marketing-pack'), 'value' => 'L', 'mergado-marketing-pack'],
            1 => ['id_option' => 1, 'name' => __('Right', 'mergado-marketing-pack'), 'value' => 'R', 'mergado-marketing-pack'],
            2 => ['id_option' => 2, 'name' => __('Bottom left', 'mergado-marketing-pack'), 'value' => 'BL', 'mergado-marketing-pack'],
            3 => ['id_option' => 3, 'name' => __('Bottom right', 'mergado-marketing-pack'), 'value' => 'BR', 'mergado-marketing-pack'],
        ];
    }

    public static function APPEARANCE_TYPES(): array
    {
        return [
            0 => ['id_option' => 0, 'name' => __('By placing the cursor over a widget', 'mergado-marketing-pack'), 'value' => 0],
            1 => ['id_option' => 1, 'name' => __('With a click', 'mergado-marketing-pack'), 'value' => 1],
        ];
    }

    /*******************************************************************************************************************
     * Get field value
     *******************************************************************************************************************/

    public function getActive(): int
    {
        return (int)get_option(static::ACTIVE, 0);
    }

    public function getWebApiKey(): string
    {
        return get_option(static::WEB_API_KEY, '');
    }

    public function getOptOut($lang): string
    {
        return get_option(static::OPT_OUT . $lang, '');
    }

    public function getWidgetActive(): int
    {
        return (int)get_option(static::WIDGET_ACTIVE, 0);
    }

    public function getWidgetDesktopPosition(): int
    {
        return (int)get_option(static::WIDGET_DESKTOP_POSITION, 0);
    }

    public function getWidgetMobilePosition(): int
    {
        return (int)get_option(static::WIDGET_MOBILE_POSITION, 0);
    }

    public function getWidgetMobileWidth(): int
    {
        return (int)get_option(static::WIDGET_MOBILE_WIDTH, 480);
    }

    public function getWidgetAppearanceType(): int
    {
        return (int)get_option(static::WIDGET_APPEARANCE_TYPE, 0);
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    public static function saveFields(array $post): void
    {
        $optLanguages = [];

        foreach (get_available_languages() as $lang) {
            $optLanguages[] = static::OPT_OUT . $lang;
        }

        $optLanguages[] = static::OPT_OUT . 'en_US';

        $otherInputs = [
            static::WEB_API_KEY,
            static::WIDGET_DESKTOP_POSITION,
            static::WIDGET_MOBILE_POSITION,
            static::WIDGET_MOBILE_WIDTH,
            static::WIDGET_APPEARANCE_TYPE
        ];

        $inputs = array_merge($optLanguages, $otherInputs);

        DatabaseManager::saveOptions($post, [
            static::ACTIVE,
            static::WIDGET_ACTIVE
        ], $inputs);
    }
}
