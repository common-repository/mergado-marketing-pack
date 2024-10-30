<?php

namespace Mergado\Service\Cookie\Plugins;

use Mergado\Service\Cookie\AbstractCookiePlugin;

class CookieYesOld extends AbstractCookiePlugin
{
    public const COOKIE_ADVERTISEMENT = 'cookielawinfo-checkbox-advertisement';
    public const COOKIE_ANALYTICAL = 'cookielawinfo-checkbox-analytics';
    public const COOKIE_FUNCTIONAL = 'cookielawinfo-checkbox-functional';

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
