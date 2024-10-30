<?php

namespace Mergado\Helper;

use Mergado\Service\LogService;

class TaxHelper
{
    const VAT = 'm_feed_vat_option';

    /**
     * Return if tax is calculated in woocommerce
     *
     * @return bool
     */
    public static function isTaxCalculated(): bool
    {
        global $wpdb;
        $tax = $wpdb->get_col($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'woocommerce_calc_taxes'));

        return array_pop($tax) === 'yes';
    }

    /**
     * Return if tax is already included in product price
     *
     * @return bool
     */
    public static function isTaxIncluded(): bool
    {
        global $wpdb;
        $taxInc = $wpdb->get_col($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'woocommerce_prices_include_tax'));

        return array_pop($taxInc) === 'yes';
    }

    /**
     * Return all rates options
     *
     * @return array|object|null
     */
    public static function getTaxRates()
    {
        global $wpdb;
        $query = $wpdb->prepare("
                SELECT DISTINCT tax_rate_country
                FROM {$wpdb->prefix}woocommerce_tax_rates
                WHERE tax_rate_country != %s
                ORDER BY tax_rate_country", '');

        return $wpdb->get_results($query);
    }

    /**
     * Return tax rate by ID
     *
     * @param $id
     * @return array|object|null
     */
    public static function getTaxRateById($id)
    {
        global $wpdb;
        $prepare = $wpdb->prepare(
            "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_id = %s
                    ORDER BY tax_rate_country", $id
        );

        $output = $wpdb->get_row($prepare)->tax_rate;

        if ($output === NULL) {
            $langISO = LanguageHelper::getLangIso();
            $output = self::getTaxRateForCountry($langISO);
        }

        return $output;
    }

    /**
     * Return tax rate with highest priority for countrycode (takes * if has higher priority)
     * @param string $countryCode
     * @return array|object|null
     */
    public static function getTaxRateForCountry($countryCode)
    {
        global $wpdb;
        $rates = $wpdb->get_row(
            $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates
                WHERE tax_rate_country = %s || tax_rate_country = ''
                ORDER BY tax_rate_priority
                DESC", $countryCode)
        );

        return $rates->tax_rate;
    }

    public static function getTaxRatesForCountry($countryCode, $tax_rate_class): float
    {
        global $wpdb;
        $prepare = $wpdb->prepare(
            "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_country = %s && tax_rate_class = %s
                    ORDER BY tax_rate_priority
                    DESC", $countryCode, $tax_rate_class
        );

        $output = @$wpdb->get_row($prepare)->tax_rate;

        if (is_null($output)) {
            global $wpdb;
            $prepare = $wpdb->prepare(
                "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_country = %s && tax_rate_class = %s
                    ORDER BY tax_rate_priority
                    DESC", '', $tax_rate_class
            );

            $output = @$wpdb->get_row($prepare)->tax_rate;

            if (is_null($output)) {
                $output = 0; // 0% if not set any rate
            }
        }

        return $output;
    }

    public static function getFeedTaxCountryCode($logContext) {
        $code = get_option(self::VAT);

        if (!$code) {
            $rates = self::getTaxRates();

            if (count($rates) > 0) {
                $code = $rates[0]->tax_rate_country;
            } else {
                $logger = LogService::getInstance();
                $logger->error('No available country tax rate', $logContext);
            }
        }

        return $code;
    }
}
