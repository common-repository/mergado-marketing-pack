<?php declare(strict_types=1);

namespace Mergado\Service\Cron;

use DOMException;
use Mergado\Exception\CronRunningException;
use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Service\LogService;
use Mergado\Service\ProductPriceImportService;
use Mergado\Traits\SingletonTrait;

class CronActionService
{
    use SingletonTrait;

    private $logger;

    public function __construct()
    {
        $this->logger = LogService::getInstance();
    }

    public function initActions(): void
    {
        add_action('wp-cron-product-feed-hook', [$this, 'actionProducts']);
        add_action('wp-cron-stock-feed-hook', [$this, 'actionStock']);
        add_action('wp-cron-category-feed-hook', [$this, 'actionCategory']);
        add_action('wp-cron-customer-feed-hook', [$this, 'actionCustomer']);
        add_action('wp-cron-import-feed-hook', [$this, 'actionImport']);
    }

    /**
     * @throws CronRunningException|DOMException
     */
    public function actionProducts(): void
    {
        $this->logger->info('========= WP_CRON: Products start ========', ProductFeed::getLogContext());
        $productFeed = new ProductFeed();
        $productFeed->generateXml();
        $this->logger->info('========= WP_CRON: Products end ========', ProductFeed::getLogContext());
    }

    /**
     * @throws CronRunningException
     */
    public function actionStock(): void
    {
        $this->logger->info('========= WP_CRON: Stock start ========', StockFeed::getLogContext());
        $stockFeed = new StockFeed();
        $stockFeed->generateXML();
        $this->logger->info('========= WP_CRON: Stock end ========', StockFeed::getLogContext());
    }

    /**
     * @throws CronRunningException
     */
    public function actionCategory(): void
    {
        $this->logger->info('========= WP_CRON: Category start ========', CategoryFeed::getLogContext());
        $categoryFeed = new CategoryFeed();
        $categoryFeed->generateXML();
        $this->logger->info('========= WP_CRON: Category end ========', CategoryFeed::getLogContext());
    }

    /**
     * @throws CronRunningException|DOMException
     */
    public function actionCustomer(): void
    {
        $this->logger->info('========= WP_CRON: Customer start ========', CustomerFeed::getLogContext());
        $customerFeed = new CustomerFeed();
        $customerFeed->generateXML();
        $this->logger->info('========= WP_CRON: Customer end ========', CustomerFeed::getLogContext());
    }

    public function actionImport(): void
    {
        $this->logger->info('========= WP_CRON: Import start ========', ProductPriceImportService::LOG_CONTEXT);
        ProductPriceImportService::getInstance()->importPrices('');
        $this->logger->info('========= WP_CRON: Import end ========', ProductPriceImportService::LOG_CONTEXT);
    }
}
