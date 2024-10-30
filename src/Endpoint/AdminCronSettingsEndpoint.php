<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Exception;
use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Manager\DatabaseManager;
use Mergado\Service\ProductPriceImportService;

class AdminCronSettingsEndpoint extends AbstractEndpoint implements EndpointInterface
{

    public function cronSettings(): void
    {
        $feed = $_POST['feed'] ?? '';

        if ($feed !== '') {
            $this->checkToken();

            try {
                switch ($feed) {
                    case 'product':
                        DatabaseManager::saveOptions($_POST, [
                            ProductFeed::getWpCronActiveDbName(),
                        ], [
                            ProductFeed::getWpCronScheduleDbName(),
                            ProductFeed::getWpCronStartDbName()
                        ]);
                        exit;

                    case 'stock':
                        DatabaseManager::saveOptions($_POST, [
                            StockFeed::getWpCronActiveDbName()
                        ], [
                            StockFeed::getWpCronScheduleDbName(),
                            StockFeed::getWpCronStartDbName()
                        ]);
                        exit;

                    case 'category':
                        DatabaseManager::saveOptions($_POST, [
                            CategoryFeed::getWpCronActiveDbName()
                        ], [
                            CategoryFeed::getWpCronScheduleDbName(),
                            CategoryFeed::getWpCronStartDbName()
                        ]);
                        exit;

                    case 'customer':
                        DatabaseManager::saveOptions($_POST, [
                            CustomerFeed::getWpCronActiveDbName()
                        ], [
                            CustomerFeed::getWpCronScheduleDbName(),
                            CustomerFeed::getWpCronStartDbName()
                        ]);
                        exit;
                    case 'import':
                        DatabaseManager::saveOptions($_POST, [
                            ProductPriceImportService::WP_CRON_ACTIVE_DB_NAME,
                        ], [
                            ProductPriceImportService::WP_CRON_SCHEDULE_DB_NAME,
                            ProductPriceImportService::WP_CRON_START_DB_NAME
                        ]);
                        exit;
                    }

                wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
            } catch (Exception $e) {
                wp_send_json_error(['error' => __('Something went wrong during save.', 'mergado-marketing-pack')]);
            }
        }
    }

    public function initEndpoints(): void
    {
        add_action('wp_ajax_ajax_save_wp_cron', [$this, 'cronSettings']);
    }
}
