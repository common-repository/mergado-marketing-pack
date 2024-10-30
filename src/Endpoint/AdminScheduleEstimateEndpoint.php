<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;

class AdminScheduleEstimateEndpoint extends AbstractEndpoint implements EndpointInterface
{
    public function getEstimate(): void
    {
        $feed = $_POST['feed'] ?? '';
        $schedule = $_POST['schedule'] ?? '';

        if ($feed !== '' && $schedule !== '') {
            $this->checkToken();

            switch ($feed) {
                case 'product':
                    $productFeed = new ProductFeed();
                    $estimate = $productFeed->getFeedEstimate($schedule);

                    wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
                    exit;
                case 'stock':
                    $stockFeed = new StockFeed();
                    $estimate = $stockFeed->getFeedEstimate($schedule);

                    wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
                    exit;
                case 'category':
                    $categoryFeed = new CategoryFeed();
                    $estimate = $categoryFeed->getFeedEstimate($schedule);

                    wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
                    exit;
                case 'customer':
                    $customerFeed = new CustomerFeed();
                    $estimate = $customerFeed->getFeedEstimate($schedule);

                    wp_send_json_success(["success" => __('Estimate ready', 'mergado-marketing-pack'), 'estimate' => $estimate]);
                    exit;
            }
        }
    }

    public function initEndpoints(): void
    {
        add_action('wp_ajax_ajax_get_schedule_estimate', [$this, 'getEstimate']);
    }
}
