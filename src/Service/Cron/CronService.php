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

namespace Mergado\Service\Cron;

use Mergado\Service\LogService;

class CronService
{
    public const CRON_NAMES = [
        'wp-cron-product-feed-hook',
        'wp-cron-stock-feed-hook',
        'wp-cron-category-feed-hook',
        'wp-cron-import-feed-hook',
    ];

    public const FORCE_ENABLE_CRON_DB_NAME = 'mmp-wp-cron-forced'; // Enable wp cron forms even with DISABLE_WP_CRON = true in wp-config.php

    /*******************************************************************************************************************
     * ADD SCHEDULE TASKS
     *******************************************************************************************************************/

    public static function addTask($task, $schedule, $start): void
    {
        $logger = LogService::getInstance();

        $hook = $task . '-hook';

        if (wp_next_scheduled($hook)) {
            // First remove old task if exist
            self::removeTask($task);
        }

        if($start !== '') {
            $time = date_create($start)->getTimestamp();
        } else {
            $time = strtotime('+30 minutes');
        }

        wp_schedule_event( $time, $schedule, $hook);

        $logger->info('TASK ADDED: ' . $task . ' - schedule - ' . $schedule . ' - start - ' . $start, 'settings');
    }

    public static function addAllTasks(): void
    {
        foreach(self::CRON_NAMES as $hook) {
            $name = explode('-hook', $hook)[0];

            $start = get_option($name . '-start');
            $schedule = get_option($name . '-schedule');

            if($start !== '') {
                $time = date_create($start . 'GMT+1')->getTimestamp();
            } else {
                $time = time();
            }

            wp_schedule_event( $time, $schedule, $hook);
        }
    }

    /*******************************************************************************************************************
     * REMOVE TASKS
     *******************************************************************************************************************/

    public static function removeTask($task): void
    {
        $logger = LogService::getInstance();

        wp_clear_scheduled_hook($task . '-hook');

        $logger->info('TASK REMOVED: ' . $task, 'settings');
    }

    public static function removeAllTasks(): void
    {
        foreach(self::CRON_NAMES as $item) {
            wp_clear_scheduled_hook($item);
        }
    }

    public static function getTaskByVariable($task)
    {
    	if ($task !== 0) {
	        return CronScheduleService::getScheduleTasks()[$task];
	    } else {
    		return '--';
	    }
    }

    /*******************************************************************************************************************
     * SETTINGS
     *******************************************************************************************************************/

    public static function isWpCronForceEnabled(): bool
    {
        return (int)get_option(self::FORCE_ENABLE_CRON_DB_NAME, 0) === 1;
    }
}
