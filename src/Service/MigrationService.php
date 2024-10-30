<?php
namespace Mergado\Service;

use Mergado\Traits\SingletonTrait;
use Mergado\Utils\MigrationUtils;

class MigrationService
{
    use SingletonTrait;

    public const MERGADO_DB_VERSION = 'mergado_db_version';

    public function migrate() {
        // Speed optimization
        if (MigrationUtils::getCurrentDbVersion() < PLUGIN_VERSION) {

            if(is_multisite()) {
                $sites = get_sites();

                foreach($sites as $site) {
                    switch_to_blog($site->blog_id);

                    $this->migrationList();

                    restore_current_blog();
                }
            } else {
                $this->migrationList();
            }
        }

    }

    /**
     *  Place for all migrations
     */
    protected function migrationList(): void
    {
        $initialDbVersion = get_option(self::MERGADO_DB_VERSION, false);

        MigrationUtils::migrateAlwaysWithoutVersionChange('2.0.0');

        MigrationUtils::migrateOnlyOnExactOrLowerVersion('2.1.5', $initialDbVersion);
        MigrationUtils::migrate('2.3.0');
        MigrationUtils::migrate('3.0.0');
        MigrationUtils::migrate('3.3.0');
        MigrationUtils::migrate('3.3.3');
        MigrationUtils::migrate('3.4.0');
        MigrationUtils::migrate('3.5.0');
    }
}
