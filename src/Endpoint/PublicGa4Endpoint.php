<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Exception;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\Ga4ServiceIntegration;

class PublicGa4Endpoint implements EndpointInterface
{
    public function getGa4CartData() : void
    {
        $ga4 = Ga4ServiceIntegration::getInstance();

        try {
            wp_send_json_success(['cart_data' => $ga4->getCartDataObject(), 'coupon' => $ga4->getCartGlobalCoupon()]);
        } catch (Exception $e) {
            wp_send_json_error(["error" => __('Error during GA4 data fetch.', 'mergado-marketing-pack')]);
        }
        exit;
    }
    public function initEndpoints(): void
    {
        add_action( 'wp_ajax_get_ga4_cart_data', [$this, 'getGa4CartData']);
        add_action( 'wp_ajax_nopriv_get_ga4_cart_data', [$this, 'getGa4CartData']);
    }
}
