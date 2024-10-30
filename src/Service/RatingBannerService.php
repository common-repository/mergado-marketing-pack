<?php

namespace Mergado\Service;

use DateTime;

class RatingBannerService
{
    public const COOKIE_RATING = 'mmp-cookie-rating';

    public static function setNextRatingTimestamp($modifier)
    {
        $now = new DateTime();

        update_option(self::COOKIE_RATING, $now->modify($modifier)->format(NewsService::DATE_FORMAT), true);
    }

    public static function shouldBeRatingVisible(): bool
    {
        $now = new DateTime();
        $nextRating = self::getNextRatingTimestamp();

        // First let 30 days pass
        if (!$nextRating) {
            self::setNextRatingTimestamp('+30 days');
            return false;
        }

        $nextRating = new DateTime($nextRating);

        return $nextRating <= $now;
    }

    public static function getNextRatingTimestamp()
    {
        return get_option(self::COOKIE_RATING);
    }
}
