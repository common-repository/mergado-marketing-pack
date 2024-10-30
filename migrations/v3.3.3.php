<?php

if (!class_exists('MergadoMigration_3_3_3')) {
    class MergadoMigration_3_3_3
    {
        public function execute()
        {
            global $wpdb;

            $table = $wpdb->prefix . 'mergado_news';

            $row = $wpdb->get_row("SELECT * FROM " . $table);

            if (!isset($row->link)) {
                include_once __MERGADO_MIGRATIONS_DIR__ . 'v3.3.0.php';
            }
        }
    }
}

$migration = new MergadoMigration_3_3_3();
$migration->execute();
