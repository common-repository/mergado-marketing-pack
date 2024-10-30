<?php declare(strict_types=1);

namespace Mergado\Service\Cron;

use Mergado\Traits\SingletonTrait;

class CronScheduleService
{
    use SingletonTrait;

    public function initSchedules(): void
    {
        // Init custom schedules
        $this->addQuarterHourSchedule();
        $this->addTenMinutesSchedule();

        $this->registerPluginUpdateAvailabilityCron();
    }

    public function addQuarterHourSchedule(): void
    {
        add_filter('cron_schedules', function ($schedules) {
            $schedules['quarterhour'] = array(
                'interval' => 15 * 60, //15 minutes * 60 seconds
                'display' => __('Every 15 minutes', 'mergado-marketing-pack'),
            );

            return $schedules;
        });
    }

    public function addTenMinutesSchedule(): void
    {
        add_filter('cron_schedules', function ($schedules) {
            if (!isset($schedules["10min"])) {
                $schedules["10min"] = array(
                    'interval' => 10 * 60,
                    'display' => __('Once every 10 minutes'));
            }

            return $schedules;
        });
    }

    public static function registerPluginUpdateAvailabilityCron(): void
    {

        /**
         * REGISTER CRON TO CHECK IF NEW PLUGIN VERSIONS AVAILABLE EVERY 5 MINUTES
         */
        if (!wp_next_scheduled('schedule_update_hook')) {
            wp_schedule_event(time(), '10min', 'schedule_update_hook');
        }

        // Add schedule
        add_action('schedule_update_hook', function () {
            wp_update_plugins();
        });
    }

    public static function getScheduleTasks(): array
    {
        return [
            'quarterhour' => __('Every 15 minutes', 'mergado-marketing-pack'),
            'hourly' => __('Every hour', 'mergado-marketing-pack'),
            'twicedaily' => __('Twice a day', 'mergado-marketing-pack'),
            'daily' => __('Daily', 'mergado-marketing-pack')
        ];
    }

    public static function getScheduleInSeconds($schedule)
    {
        if ($schedule === 'quarterhour') {
            return 900;
        } else if ($schedule === 'hourly') {
            return 3600;
        } else if ($schedule === 'twicedaily') {
            return 43200;
        } else if ($schedule === 'daily') {
            return 86400;
        }
    }
}
