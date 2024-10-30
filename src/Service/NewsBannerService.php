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

namespace Mergado\Service;

use DateTime;
use Exception;

class NewsBannerService
{
    public const NEXT_BANNER_VISIBILITY_DB_NAME = 'mmp-cookie-news';

    public static function setNextBannerVisibility($modifier): void
    {
        $now = new DateTime();

        update_option(self::NEXT_BANNER_VISIBILITY_DB_NAME, $now->modify($modifier)->format(NewsService::DATE_FORMAT), true);
    }

    /**
     * @throws Exception
     */
    public static function shouldBeVisible(): bool
    {
        $now = new DateTime();
        $nextRating = self::getNextBannerVisibility();

        // First let 30 days pass
        if (!$nextRating) {
            return true;
        }

        $nextRating = new DateTime($nextRating);

        return $nextRating <= $now;
    }

    public static function getNextBannerVisibility()
    {
        return get_option(self::NEXT_BANNER_VISIBILITY_DB_NAME);
    }
}
