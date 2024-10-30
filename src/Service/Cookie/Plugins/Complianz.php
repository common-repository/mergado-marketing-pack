<?php

namespace Mergado\Service\Cookie\Plugins;

use Mergado\Service\Cookie\AbstractCookiePlugin;

class Complianz extends AbstractCookiePlugin
{
    public const COOKIE_ADVERTISEMENT = 'cmplz_marketing';
    public const COOKIE_ANALYTICAL = 'cmplz_statistics';
    public const COOKIE_FUNCTIONAL = 'cmplz_functional';

    public function isFunctionalActive(): bool
    {
        return $this->isCookieActive(self::COOKIE_FUNCTIONAL);
    }

    public function isAnalyticalActive(): bool
    {
        return $this->isCookieActive(self::COOKIE_ANALYTICAL);
    }

    public function isAdvertisementActive(): bool
    {
        return $this->isCookieActive(self::COOKIE_ADVERTISEMENT);
    }
}
