<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 */

use Mergado\Manager\TokenManager;
use Mergado\Service\Cron\CronService;
use Mergado\Service\MigrationService;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */
class Mergado_Marketing_Pack_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        TokenManager::generateOrGetTokenIfExists();
        MigrationService::getInstance()->migrate();

        CronService::addAllTasks();
        flush_rewrite_rules();
    }

}
