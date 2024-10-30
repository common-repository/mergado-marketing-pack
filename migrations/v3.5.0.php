<?php

if (!class_exists('MergadoMigration_3_5_0')) {
    class MergadoMigration_3_5_0
    {
        public function execute()
        {
            $active = get_option('mergado_google_analytics_active', false);
            $code = get_option('mergado_google_analytics_code', false);
            $ecommerce = get_option('mergado_google_analytics_ecommerce', false);
            $enhancedEcommerce = get_option('mergado_google_analytics_ecommerce_enhanced', false);
            $vatIncluded = get_option('gtagjs-vat-included', false);

            if ($active) {
                update_option('mmp-ga-ua-active', $active);
            }

            if ($code) {
                update_option('mmp-ga-ua-code', $code);
            }

            if ($ecommerce) {
                update_option('mmp-ga-ua-ecommerce', $ecommerce);
            }

            if ($enhancedEcommerce) {
                update_option('mmp-ga-ua-enhanced-ecommerce', $enhancedEcommerce);
            }

            if ($vatIncluded) {
                update_option('mmp-ga-ua-vat-included', $vatIncluded);
            }

            delete_option('mergado_google_analytics_tracking'); // Do not migrate, deleted
            delete_option('mergado_google_analytics_active');
            delete_option('mergado_google_analytics_code');
            delete_option('mergado_google_analytics_ecommerce');
            delete_option('mergado_google_analytics_ecommerce_enhanced');
            delete_option('gtagjs-vat-included');
            delete_option('ga_refund_active'); // Remove of old field
            delete_option('ga_refund_code'); // Remove of old field
        }
    }
}

$migration = new MergadoMigration_3_5_0();
$migration->execute();
