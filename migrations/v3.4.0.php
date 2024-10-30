<?php

if (!class_exists('MergadoMigration_3_4_0')) {
    class MergadoMigration_3_4_0
    {
        public function execute()
        {
            // Set default values for visibility OTHER section - checkboxes
            add_option('mmp-wp-product-feed-visibility-other--public', '1');
            add_option('mmp-wc-product-feed-visibility-other--visible', '1');
            add_option('mmp-wc-product-feed-visibility-other--catalog', '1');
            add_option('mmp-wc-product-feed-visibility-other--search', '1');
            add_option('mmp-wc-product-feed-visibility-other--hidden', '1');

            // Set default values for visibility PRODUCT section - checkboxes
            add_option('mmp-wp-product-feed-visibility-product--public', '1');
            add_option('mmp-wc-product-feed-visibility-product--visible', '1');
            add_option('mmp-wc-product-feed-visibility-product--catalog', '1');
            add_option('mmp-wc-product-feed-visibility-product--search', '1');
            add_option('mmp-wc-product-feed-visibility-product--hidden', '1');
        }
    }
}

$migration = new MergadoMigration_3_4_0();
$migration->execute();
