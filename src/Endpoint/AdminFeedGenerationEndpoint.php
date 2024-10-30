<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Exception;
use Mergado\Exception\CronRunningException;
use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Service\AlertService;
use Mergado\Service\ProductPriceImportService;

class AdminFeedGenerationEndpoint extends AbstractEndpoint implements EndpointInterface
{
    /**
     */
    public function generateFeed(): void
    {
        $feed = $_POST['feed'] ?? '';
        $force = $_POST['force'] ?? false; // forced generating manual
        $firstRun = $_POST['firstRun'] ?? false; // forced generating manual

        if ($feed !== '') {
            $this->checkToken();

            switch ($feed) {
                case 'productCron':
                    try {
                        $productFeed = new ProductFeed();

                        if ($firstRun) {
                            $productFeed->deleteLoweredItemsPerStep();
                        }

                        if ($firstRun && $productFeed->hasFeedFailed()) {
                            $productFeed->setLowerItemsPerStep($productFeed->getDefaultItemsPerStep());
                        }

                        $result = $productFeed->generateXml($force);
                        $percentage = $productFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($productFeed->getLoweredItemsPerStep() !== 0) {
                            $productFeed->setLoweredItemsPerStepAsMain();
                        }

                        $alertService = AlertService::getInstance();
                        $alertService->setErrorInactive('product', AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(["success" => __('Product feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['error' => __('Product feed generating already running. Please wait a minute and try it again.', 'mergado-marketing-pack')], 412);
                    } catch (Exception $e) {
                        wp_send_json_error(['success' => __('Product feed generation failed. Check logs for more information.', 'mergado-marketing-pack')], 500);
                    }
                    exit;

                case 'stockCron':
                    try {
                        $stockFeed = new StockFeed();

                        if ($firstRun) {
                            $stockFeed->deleteLoweredItemsPerStep();
                        }

                        if ($firstRun && $stockFeed->hasFeedFailed()) {
                            $stockFeed->setLowerItemsPerStep($stockFeed->getDefaultItemsPerStep());
                        }

                        $result = $stockFeed->generateXML($force);
                        $percentage = $stockFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($stockFeed->getLoweredItemsPerStep() !== 0) {
                            $stockFeed->setLoweredItemsPerStepAsMain();;
                        }

                        $alertService = AlertService::getInstance();
                        $alertService->setErrorInactive('stock', AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(['success' => __('Heureka availability feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['error' => __('Heureka availability feed already running.', 'mergado-marketing-pack')], 412);
                    } catch (Exception $e) {
                        wp_send_json_error(['success' => __('Heureka availability feed generation failed. Check logs for more information.', 'mergado-marketing-pack')], 500);
                    }
                    exit;

                case 'categoryCron':
                    try {
                        $categoryFeed = new CategoryFeed();

                        if ($firstRun) {
                            $categoryFeed->deleteLoweredItemsPerStep();
                        }

                        if ($firstRun && $categoryFeed->hasFeedFailed()) {
                            $categoryFeed->setLowerItemsPerStep($categoryFeed->getDefaultItemsPerStep());
                        }

                        $result = $categoryFeed->generateXML($force);

                        $percentage = $categoryFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($categoryFeed->getLoweredItemsPerStep() !== 0) {
                            $categoryFeed->setLoweredItemsPerStepAsMain();
                        }

                        $alertService = AlertService::getInstance();
                        $alertService->setErrorInactive('category', AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(['success' => __('Category feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['error' => __('Category feed already running.', 'mergado-marketing-pack')], 412);
                    } catch (Exception $e) {
                        wp_send_json_error(['success' => __('Category feed generation failed. Check logs for more information.', 'mergado-marketing-pack')], 500);
                    }
                    exit;

                case 'customerCron':
                    try {
                        $customerFeed = new CustomerFeed();

                        if ($firstRun) {
                            $customerFeed->deleteLoweredItemsPerStep();
                        }


                        if ($firstRun && $customerFeed->hasFeedFailed()) {
                            $customerFeed->setLowerItemsPerStep($customerFeed->getDefaultItemsPerStep());
                        }

                        $result = $customerFeed->generateXML($force);
                        $percentage = $customerFeed->getFeedPercentage();

                        // Save lowered value as main if cron is ok without internal error
                        if ($customerFeed->getLoweredItemsPerStep() !== 0) {
                            $customerFeed->setLoweredItemsPerStepAsMain();
                        }

                        $alertService = AlertService::getInstance();
                        $alertService->setErrorInactive('customer', AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_success(['success' => __('Customer feed generated', 'mergado-marketing-pack'), 'feedStatus' => $result, 'percentage' => $percentage]);
                    } catch (CronRunningException $e) {
                        wp_send_json_error(['error' => __('Customer feed already running.', 'mergado-marketing-pack')], 412);
                    } catch (Exception $e) {
                        wp_send_json_error(['success' => __('Customer feed generation failed. Check logs for more information.', 'mergado-marketing-pack')], 500);
                    }
                    exit;

                case 'importPrices':
                    $productPriceImportServices = ProductPriceImportService::getInstance();
                    $result = $productPriceImportServices->importPrices('');

                    // Save lowered value as main if cron is ok without internal error
                    if ($productPriceImportServices->getLoweredProductsPerStep() !== 0) {
                        $productPriceImportServices->setLoweredProductsPerStepAsMain();
                    }

                    if ($result) {
                        wp_send_json_success(['success' => __('Mergado prices imported', 'mergado-marketing-pack'), 'feedStatus' => $result]);
                    } else {
                        wp_send_json_error(['error' => __('Error importing prices. Do you have correct URL in settings?', 'mergado-marketing-pack')], 424);
                    }
                    exit;
            }
        }
    }

    public function lowerCronProductStep(): void
    {
        $feed = $_POST['feed'] ?? '';

        if ($feed !== '') {
            $this->checkToken();

            switch ($feed) {
                case 'product':
                    $productFeed = new ProductFeed();
                    $productFeed->setGenerationStep(0);
                    $productFeed->deleteTemporaryFiles();

                    $loweredPerStep = $productFeed->lowerItemsPerStep();

                    if ($loweredPerStep) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep], 200);
                    } else {
                        //Not prestashop so simple
                        $alertService = AlertService::getInstance();
                        $alertService->setErrorActive($feed, AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);


                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
                case 'stock':
                    $stockFeed = new StockFeed();
                    $stockFeed->setGenerationStep(0);
                    $stockFeed->deleteTemporaryFiles();

                    if ($loweredPerStep = $stockFeed->lowerItemsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {
                        //Not prestashop so simple
                        $alertService = AlertService::getInstance();
                        $alertService->setErrorActive($feed, AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);

                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
                case 'category':
                    $categoryFeed = new CategoryFeed();
                    $categoryFeed->setGenerationStep(0);
                    $categoryFeed->deleteTemporaryFiles();

                    if ($loweredPerStep = $categoryFeed->lowerItemsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {

                        //Not prestashop so simple
                        $alertService = AlertService::getInstance();
                        $alertService->setErrorActive($feed, AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);
                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                case 'customer':
                    $customerFeed = new CustomerFeed();
                    $customerFeed->setGenerationStep(0);
                    $customerFeed->deleteTemporaryFiles();

                    if ($loweredPerStep = $customerFeed->lowerItemsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {
                        //Not prestashop so simple
                        $alertService = AlertService::getInstance();
                        $alertService->setErrorActive($feed, AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);
                        wp_send_json_error(['error' => __('Something went wrong. Feed can\'t be generated.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
                case 'import':
                    if ($loweredPerStep = ProductPriceImportService::getInstance()->lowerProductsPerStep()) {
                        wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack'), "loweredCount" => $loweredPerStep]);
                    } else {
                        wp_send_json_error(['error' => __('Something went wrong. Prices can\'t be imported.', 'mergado-marketing-pack')], 500);
                    }

                    exit;
            }
        }
    }

    public function saveImportUrl(): void
    {
        $url = $_POST['url'] ?? '';

        $this->checkToken();

        $result = ProductPriceImportService::getInstance()->setImportUrl($url);

        if ($result) {
            wp_send_json_success(["success" => __('Settings saved', 'mergado-marketing-pack')]);
        } else {
            wp_send_json_error(['error' => __('Something went wrong. Import url can\'t be saved.', 'mergado-marketing-pack')], 500);
        }

        exit;
    }

    public function initEndpoints(): void
    {
        add_action('wp_ajax_ajax_generate_feed', [$this, 'generateFeed']);
        add_action('wp_ajax_ajax_lower_cron_product_step', [$this, 'lowerCronProductStep']);
        add_action('wp_ajax_ajax_save_import_url', [$this, 'saveImportUrl']);
    }
}
