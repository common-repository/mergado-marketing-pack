<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Exception;
use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Manager\DatabaseManager;
use Mergado\Service\ProductPriceImportService;

class AdminWizardEndpoint extends AbstractEndpoint implements EndpointInterface
{
    public function setWizardCompleted(): void
    {
        $feed = $_POST['feed'] ?? '';

        if ($feed !== '') {
            $this->checkToken();

            try {
                switch ($feed) {
                    case 'product':
                        $productFeed = new ProductFeed();

                        DatabaseManager::saveOptions(
                            [$productFeed->wizardFinishedDbName => 'on'],
                            [$productFeed->wizardFinishedDbName],
                            []
                        );
                        exit;

                    case 'stock':
                        $stockFeed = new StockFeed();

                        DatabaseManager::saveOptions(
                            [$stockFeed->wizardFinishedDbName => 'on'],
                            [$stockFeed->wizardFinishedDbName],
                            []);
                        exit;

                    case 'category':
                        $categoryFeed = new CategoryFeed();

                        DatabaseManager::saveOptions(
                            [$categoryFeed->wizardFinishedDbName => 'on'],
                            [$categoryFeed->wizardFinishedDbName],
                            []);
                        exit;

                    case 'customer':
                        $customerFeed = new CustomerFeed();

                        DatabaseManager::saveOptions(
                            [$customerFeed->wizardFinishedDbName => 'on'],
                            [$customerFeed->wizardFinishedDbName],
                            []);
                        exit;
                    case 'import':
                        DatabaseManager::saveOptions([ProductPriceImportService::WIZARD_FINISHED_DB_NAME => 'on'], [[ProductPriceImportService::WIZARD_FINISHED_DB_NAME],], []);

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
        add_action('wp_ajax_ajax_set_wizard_complete', [$this, 'setWizardCompleted']);
    }
}
