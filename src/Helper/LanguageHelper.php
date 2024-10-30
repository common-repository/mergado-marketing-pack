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

namespace Mergado\Helper;

class LanguageHelper
{
    public const MERGADO_TO_DOMAIN = [
        'cz' => 'cz',
        'sk' => 'sk',
        'hu' => 'hu',
        'pl' => 'pl',
        'hr' => 'hr',
        'sl' => 'si', // slovenia
        'de' => 'de',
        'de_AT' => 'at',
        'de_CH' => 'ch', // switzerland
        'rs' => 'rs', // serbia
        'sr_RS' => 'rs',
        'other' => 'com',
    ];

    public const PACK_LANG_TO_DOMAIN = [
        'cz' => 'cz',
        'sk' => 'cz',
        'hu' => 'hu',
        'other' => 'com',
    ];

    public static function getPackDomain()
    {
        return self::getDomain(self::PACK_LANG_TO_DOMAIN);
    }

    public static function getMergadoDomain()
    {
        return self::getDomain(self::MERGADO_TO_DOMAIN);
    }

    private static function getDomain($domains)
    {
        $lang = self::getUserLangCode();
        $langIso = self::getUserFullLangIso();

        $lang = strtolower($lang);

        // If exist full iso for example: swiss deutsch
        if (array_key_exists($langIso, $domains)) {
            return $domains[$langIso];
        }

        if (array_key_exists($lang,$domains)) {
            return $domains[$lang];
        }

        return $domains['other'];
    }

    public static function getLang(): string
    {
        $lang = self::getLangIso();

        if ($lang === 'CS') {
            $lang = 'CZ';
        }

        return $lang;
    }

    /**
     * Use in administration
     */
    public static function getUserFullLangIso(): string
    {
        return self::getLocale();
    }

    /**
     * Use in administration
     */
    public static function getUserLangCode(): string
    {
        return strtoupper(explode('_', self::getLocale())[0]);
    }

    public static function getLangIso(): string
    {
        return strtoupper(explode('_', get_locale())[0]);
    }

    public static function getLocale(): string
    {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }

        return $locale;
    }
}
