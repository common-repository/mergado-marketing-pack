<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Mergado\Service\NewsService;

class AdminNewsEndpoint extends AbstractEndpoint implements EndpointInterface
{
    public function news(): void
    {
        $todo = $_POST['todo'] ?? '';
        $id = $_POST['id'] ?? '';

        if ($todo !== '') {
            $this->checkToken();

            if ($todo === 'mmp-set-readed') {
                NewsService::setArticlesShown([$id]);
                exit;
            }
        }
    }

    public function initEndpoints(): void
    {
        add_action('wp_ajax_ajax_news', [$this, 'news']);
    }
}
