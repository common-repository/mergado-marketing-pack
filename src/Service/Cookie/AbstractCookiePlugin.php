<?php

namespace Mergado\Service\Cookie;

use Mergado\Service\CookieService;

abstract class AbstractCookiePlugin
{
    public function isCookieActive( string $cookieName ): bool
    {
        return CookieService::isCookieActive($cookieName);
    }

    public function isActiveStatus($cookieValue): bool
    {
        return CookieService::isActiveStatus($cookieValue);
    }
}
