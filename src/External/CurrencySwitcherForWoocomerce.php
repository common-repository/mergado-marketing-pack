<?php

namespace Mergado\External;

class CurrencySwitcherForWoocomerce
{
    /**
     * Set plugin currency to default value during feed generation
     *
     * @return void
     */
    public static function algSwitcherDisable(): void
    {
        if (isset($_SESSION['alg_currency'])) {
            $_SESSION['mergado__alg_currency'] = $_SESSION['alg_currency'];
            $_SESSION['alg_currency'] = get_option( 'woocommerce_currency' );
        }
    }

    /**
     * Set plugin back to original value and delete data
     **/
    public static function algSwitcherEnable()
    {
        if (isset($_SESSION['mergado__alg_currency'], $_SESSION['alg_currency'])) {
            $_SESSION['alg_currency'] = $_SESSION['mergado__alg_currency'];
            unset($_SESSION['mergado__alg_currency']);
        }
    }
}
