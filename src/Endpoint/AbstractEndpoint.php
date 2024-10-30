<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Mergado\Manager\TokenManager;

class AbstractEndpoint
{
    protected function checkToken(): void
    {
        $token = $_POST['token'] ?? '';

        if ($token !== TokenManager::getToken()) {
            wp_send_json_error(['error' => __('Invalid token', 'mergado-marketing-pack')]);
            exit;
        }
    }
}
