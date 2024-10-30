<?php

namespace Mergado\Utils;

use Mergado\Service\MigrationService;

class MigrationUtils
{
    /**
     * Run specific migration for site (if multisite, apply to all)
     *
     * @param string $version
     * @return void
     */
    public static function migrate(string $version): void
    {
        if(!get_option(MigrationService::MERGADO_DB_VERSION) || get_option(MigrationService::MERGADO_DB_VERSION) < $version) {
            include __MERGADO_MIGRATIONS_DIR__ . sprintf('v%s.php', $version);
            self::updateDbVersion($version);
        }
    }

    /**
     * Run specific migration for site (if multisite, apply to all)
     * Do not execute migration on new instalations
     *
     *
     * @param string $version
     * @param $initalDbVersion
     * @return void
     */
    public static function migrateOnlyOnExactOrLowerVersion(string $version, $initalDbVersion): void
    {
        if((PLUGIN_VERSION === $version && get_option(MigrationService::MERGADO_DB_VERSION) !== $version) || (get_option(MigrationService::MERGADO_DB_VERSION) !== false && get_option(MigrationService::MERGADO_DB_VERSION) < $initalDbVersion)) {
            include __MERGADO_MIGRATIONS_DIR__ . sprintf('v%s.php', $version);
            self::updateDbVersion($version);
        }
    }

    /**
     * Run specific migration for site (if multisite, apply to all)
     *
     * @param string $version
     * @return void
     */
    public static function migrateAlwaysWithoutVersionChange(string $version): void
    {
        include __MERGADO_MIGRATIONS_DIR__ . sprintf('v%s.php', $version);
    }

    private static function updateDbVersion($version): void
    {
        update_option(MigrationService::MERGADO_DB_VERSION, $version, true);
    }

    public static function getCurrentDbVersion() {
        return get_option(MigrationService::MERGADO_DB_VERSION, false);
    }
}
