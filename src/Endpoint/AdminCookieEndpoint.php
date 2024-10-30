<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Mergado\Service\NewsBannerService;
use Mergado\Service\RatingBannerService;

class AdminCookieEndpoint extends AbstractEndpoint implements EndpointInterface
{
    public function addCookie(): void
    {
        $cookie = $_POST['cookie'] ?? '';

        if ($cookie !== '') {
            $this->checkToken();

            switch ($cookie) {
                case 'mmp-cookie-rating':
                    RatingBannerService::setNextRatingTimestamp('+14 days');
                    exit;

                case 'mmp-cookie-news':
                    NewsBannerService::setNextBannerVisibility('+14 days');
                    exit;
            }
        }
    }

    public function initEndpoints(): void
    {
        add_action('wp_ajax_ajax_cookie', [$this, 'addCookie']);
    }
}
