<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 */

use Mergado\Service\Cron\CronService;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */
class Mergado_Marketing_Pack_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        CronService::removeAllTasks();
        flush_rewrite_rules();
    }
}
