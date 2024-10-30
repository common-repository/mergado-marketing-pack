<?php

if (!class_exists('MergadoMigration_3_3_0')) {
    class MergadoMigration_3_3_0
    {
        public function execute()
        {
            global $wpdb;

            $table = $wpdb->prefix . 'mergado_news';

            $query = "ALTER TABLE `" . $table . "` ADD `link` varchar(255) NOT NULL AFTER `category`";

            $wpdb->query($query);
        }
    }
}

$migration = new MergadoMigration_3_3_0();
$migration->execute();
