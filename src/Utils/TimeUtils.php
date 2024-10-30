<?php declare(strict_types=1);

namespace Mergado\Utils;

class TimeUtils
{
    /**
     * Determines the difference between two timestamps.
     *
     * The difference is returned in a human readable format such as "1 hour",
     * "5 mins", "2 days".
     *
     * @since 1.5.0
     * @since 5.3.0 Added support for showing a difference in seconds.
     *
     * @param int $from Unix timestamp from which the difference begins.
     * @param int $to   Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
     * @return string Human readable time difference.
     */
    public static function humanTimeDiff( $from, $to = 0 ) : string {
        if ( empty( $to ) ) {
            $to = time();
        }

        $diff = (int) abs( $to - $from );

        if ( $diff < MINUTE_IN_SECONDS ) {
            $secs = $diff;
            if ( $secs <= 1 ) {
                $secs = 1;
            }
            /* translators: Time difference between two dates, in seconds. %s: Number of seconds. */
            $since = sprintf( _n( '%s second', '%s seconds', $secs, 'mergado-marketing-pack' ), $secs );
        } elseif ( $diff < HOUR_IN_SECONDS && $diff >= MINUTE_IN_SECONDS ) {
            $mins = round( $diff / MINUTE_IN_SECONDS );
            if ( $mins <= 1 ) {
                $mins = 1;
            }
            /* translators: Time difference between two dates, in minutes (min=minute). %s: Number of minutes. */
            $since = sprintf( _n( '%s min', '%s mins', $mins, 'mergado-marketing-pack' ), $mins );
        } elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
            $hours = round( $diff / HOUR_IN_SECONDS );
            if ( $hours <= 1 ) {
                $hours = 1;
            }
            /* translators: Time difference between two dates, in hours. %s: Number of hours. */
            $since = sprintf( _n( '%s hour', '%s hours', $hours, 'mergado-marketing-pack' ), $hours );
        } elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
            $days = round( $diff / DAY_IN_SECONDS );
            if ( $days <= 1 ) {
                $days = 1;
            }
            /* translators: Time difference between two dates, in days. %s: Number of days. */
            $since = sprintf( _n( '%s day', '%s days', $days, 'mergado-marketing-pack' ), $days );
        } elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
            $weeks = round( $diff / WEEK_IN_SECONDS );
            if ( $weeks <= 1 ) {
                $weeks = 1;
            }
            /* translators: Time difference between two dates, in weeks. %s: Number of weeks. */
            $since = sprintf( _n( '%s week', '%s weeks', $weeks, 'mergado-marketing-pack' ), $weeks );
        } elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
            $months = round( $diff / MONTH_IN_SECONDS );
            if ( $months <= 1 ) {
                $months = 1;
            }
            /* translators: Time difference between two dates, in months. %s: Number of months. */
            $since = sprintf( _n( '%s month', '%s months', $months, 'mergado-marketing-pack' ), $months );
        } elseif ( $diff >= YEAR_IN_SECONDS ) {
            $years = round( $diff / YEAR_IN_SECONDS );
            if ( $years <= 1 ) {
                $years = 1;
            }
            /* translators: Time difference between two dates, in years. %s: Number of years. */
            $since = sprintf( _n( '%s year', '%s years', $years, 'mergado-marketing-pack' ), $years );
        }

        /**
         * Filters the human readable difference between two timestamps.
         *
         * @since 4.0.0
         *
         * @param string $since The difference in human readable text.
         * @param int    $diff  The difference in seconds.
         * @param int    $from  Unix timestamp from which the difference begins.
         * @param int    $to    Unix timestamp to end the time difference.
         */
        return apply_filters( 'human_time_diff', $since, $diff, $from, $to );
    }
}
