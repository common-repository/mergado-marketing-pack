<?php

if (!class_exists('MergadoMigration_2_3_4')) {
    class MergadoMigration_2_3_4
    {
        public function execute()
        {
            $value = get_option('m_feed_vat_option');

            if ($value) {
                $countryCode = $this->getTaxCodeById($value);

                if (!$countryCode) {
                    update_option('m_feed_vat_option', '');
                } else {
                    update_option('m_feed_vat_option', $countryCode);
                }
            }
        }

        private function getTaxCodeById($id)
        {
            global $wpdb;
            $prepare = $wpdb->prepare(
                "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_id = %s
                    ORDER BY tax_rate_country", $id
            );

            return $wpdb->get_row($prepare)->tax_rate_country;
        }
    }
}

$migration = new MergadoMigration_2_3_4();
$migration->execute();
