<?php

namespace Mergado\Service\Cookie\Plugins;

use Mergado\Service\Cookie\AbstractCookiePlugin;

class CookieBot extends AbstractCookiePlugin
{
    public const COOKIE_DATA = 'CookieConsent';
    public const SUB_COOKIE_ADVERTISEMENT = 'marketing';
    public const SUB_COOKIE_ANALYTICAL = 'statistics';
    public const SUB_COOKIE_FUNCTIONAL = 'preferences';

    public function isFunctionalActive(): bool
    {
        return $this->isCookieActive(self::SUB_COOKIE_FUNCTIONAL);
    }

    public function isAnalyticalActive(): bool
    {
        return $this->isCookieActive(self::SUB_COOKIE_ANALYTICAL);
    }

    public function isAdvertisementActive(): bool
    {
        return $this->isCookieActive(self::SUB_COOKIE_ADVERTISEMENT);
    }

    public function isCookieActive(string $cookieName): bool
    {
        $serializedCookieObject = $_COOKIE[self::COOKIE_DATA] ?? null;

        if ($serializedCookieObject) {
            $cookieObject = $this->deserializeCookieObject($serializedCookieObject);

            if (isset($cookieObject[$cookieName])) {
                return $this->isActiveStatus($cookieObject[$cookieName]);
            }
        }

        return false;
    }

    public function deserializeCookieObject(string $deserializedCookie)
    {
        $valid_php_json = preg_replace(
            '/\s*:\s*(\w+?)([}\[,])/',
            ':"$1"$2',
            preg_replace(
                '/([{\[,])\s*(\w+?):/',
                '$1"$2":',
                str_replace( "'", '"', stripslashes( $deserializedCookie ) )
            )
        );

        return json_decode($valid_php_json, true);
    }
}
