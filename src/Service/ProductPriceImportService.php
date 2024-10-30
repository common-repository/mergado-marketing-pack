<?php
/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Service;

use Exception;
use Mergado\Exception\MissingUrlException;
use Mergado\Manager\DirectoryManager;
use Mergado\Manager\TokenManager;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\MultisiteUtils;
use Mergado\Utils\RequestUtils;
use SimpleXMLElement;

class ProductPriceImportService
{
    use SingletonTrait;

    protected $logger;

    /**
     * @var int|string
     */
    public $currentBlogId;

    public $token;

    /**
     * @var string
     */
    private $TMP_DIR_FOLDER;

    public const FILE_NAMES = array(
        'MAIN' => 'pricesImport.xml',
        'PROGRESS' => 'progressFile.xml',
    );

    public const LOG_CONTEXT = 'product-price-import';

    public const FEED_PRODUCTS_USER = 'mergado-feed-form-import-user';
    public const LAST_UPDATE_DB_NAME = 'mergado_last_prices_import';
    public const COUNT_DB_NAME = 'import-form-products';

    public const OPTIMIZATION_DB_NAME = 'import-form-product';
    public const WIZARD_FINISHED_DB_NAME = 'mmp-wizard-finished-import';

    public const WP_CRON_ACTIVE_DB_NAME = 'wp-cron-import-feed-active';
    public const WP_CRON_SCHEDULE_DB_NAME = 'wp-cron-import-feed-schedule';
    public const WP_CRON_START_DB_NAME = 'wp-cron-import-feed-start';

    public const IMPORT_URL = 'import_product_prices_url';

    public function __construct()
    {
        $this->logger = LogService::getInstance();

        $this->currentBlogId = MultisiteUtils::getCurrentBlogId();
        $this->token = TokenManager::getToken();

        // Temporary folder for saving
        $this->TMP_DIR_FOLDER = __MERGADO_TMP_DIR__ . $this->currentBlogId . '/importPrices/';

        // Create neccessary folders
        $this->createTemporaryFolders();
    }

    /**
     * Download or get data, update product info and save progress XML. Delete or change name of progress XML if not empty.
     * @return bool
     */
    public function importPrices($page, $redirect = false)
    {
        $this->logger->info('-- Import prices started --', self::LOG_CONTEXT);
        $result = '';

        try {
            if($data = $this->downloadPrices()) {
                $loop = 1;

                $this->logger->info('Importing products', self::LOG_CONTEXT);

                $itemsToImport = (int) get_option(self::COUNT_DB_NAME, 0);
                while((array) $data->ITEM != []) {
                    if (($loop <= $itemsToImport) || $itemsToImport == 0) {
                        $this->updateProduct($data->ITEM);
                        unset($data->ITEM[0]);
                        $this->saveProgressFile($data);
                        $loop++;
                    } else {
                        $result = 'hitTheLimit';
                        break;
                    }
                }

                $this->logger->info('Products imported successfully', self::LOG_CONTEXT);

                if((array) $data->ITEM != []) {
                    unlink($this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
                    rename($this->TMP_DIR_FOLDER . self::FILE_NAMES['PROGRESS'], $this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
                } else {
                    unlink($this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN']);
                    unlink($this->TMP_DIR_FOLDER . self::FILE_NAMES['PROGRESS']);
                }
            }

            $this->logger->info('--- Mergado import prices ended ---', self::LOG_CONTEXT);

        } catch (MissingUrlException $ex) {
            return false;
        } catch (Exception $ex) {
            $this->logger->error('Error importing new product prices from Mergado feed.' . $ex->getMessage(),self::LOG_CONTEXT);
        }

        if ($redirect) {
            wp_redirect('admin.php?page=' . $page);
            exit;
        }

        if ($result === 'hitTheLimit') {
            return 'stepGenerated';
        } else {
            return 'finished';
        }
    }


    /**
     * Download Prices or retrieve file from tmp folder
     *
     * @throws Exception
     * @throws MissingUrlException
     */
    public function downloadPrices()
    {
        $this->logger->info('Downloading mergado prices feed', self::LOG_CONTEXT);
        $importPriceUrl = $this->getImportUrl();

        if($importPriceUrl != '') {
            if($feed = RequestUtils::fileGetContents($importPriceUrl, 30, 5)) {
                $x = new SimpleXMLElement($feed);

                $importFinished = $this->lastImportFinished();

                // File not exists && build dates in files are not same
                if ($importFinished && $this->isNewPriceFile($x->LAST_BUILD_DATE)) {
                    $this->saveTemporaryFile($x);
                    $this->setLastImportDate($x->LAST_BUILD_DATE);

                    return $x;
                // File exists
                } elseif (!$importFinished) {
                    $this->logger->info('Last import not finished. Old file will be used', self::LOG_CONTEXT);
                    $tempFile = $this->getTempFile();
                    $x = new SimpleXMLElement($tempFile);

                    return $x;
                }
            } else {
                $this->logger->error('No data returned', self::LOG_CONTEXT);
                throw new MissingUrlException('Missing import prices feed URL');

            }
        } else {
            $this->logger->error('Missing import prices feed URL', self::LOG_CONTEXT);
            throw new MissingUrlException('Missing import prices feed URL');
        }

        $this->logger->info('No new prices for import', self::LOG_CONTEXT);
        return false;
    }


    /**
     * Set date of last downlaoded and saved XML
     *
     * @param $date
     * @throws Exception
     */
    public function setLastImportDate($date): void
    {
        try {
            $date = new \DateTime($date);
            update_option(self::LAST_UPDATE_DB_NAME, $date->format(NewsService::DATE_FORMAT), true);
        } catch (Exception $e) {
            throw new Exception('Feed contains incorrect Date format! Import failed.');
        }
    }


    /**
     * Save downloaded Mergado XML
     * @throws Exception
     */
    public function saveTemporaryFile($data): void
    {
        $filename = $this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN'];

        if ($this->lastImportFinished()) {
            file_put_contents($filename, $data->asXml());
        } else {
            throw new Exception('Previous import not finished! File exists.');
        }
    }


    /**
     * Save xml with progress data
     * @throws Exception
     */
    public function saveProgressFile($data): void
    {
        try {
            $dirFolder = $this->TMP_DIR_FOLDER;
            $filename = $dirFolder . self::FILE_NAMES['PROGRESS'];

            file_put_contents($filename, $data->asXml());
        } catch (Exception $ex) {
            $this->logger->error('Error saving progress file' . $ex, self::LOG_CONTEXT);
            throw new Exception('Error saving progress file');
        }
    }


    /**
     * Return if price file is updated or already imported before
     * @throws Exception
     */
    public function isNewPriceFile($date): bool
    {
        try {
            $date = new \DateTime($date);
            $dbDate = new \DateTime(get_option(self::LAST_UPDATE_DB_NAME), new \DateTimeZone('+00:00'));

            return $date != $dbDate;

        } catch (Exception $ex) {
            $this->logger->error("DateTime error in isNewPriceFile function\n" . $ex->getMessage(), self::LOG_CONTEXT);
            return false;
        }
    }


    /**
     * Returns if last import is finished
     */
    public function lastImportFinished(): bool
    {
        $dir = $this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN'];

        return !file_exists($dir);
    }


    /**
     * Get temporary file
     *
     * @return false|string
     * @throws Exception
     */
    public function getTempFile()
    {
        try {
            return RequestUtils::fileGetContents($this->TMP_DIR_FOLDER . self::FILE_NAMES['MAIN'], 30, 5);
        } catch (Exception $ex) {
            $this->logger->warning('XML File deleted', self::LOG_CONTEXT);
            throw new Exception('XML File deleted.');
        }
    }

    public function getImportUrl()
    {
        return get_option(self::IMPORT_URL, '');
    }

    public function setImportUrl($url): bool
    {
        if ($this->getImportUrl() === $url) {
            return true;
        }

        return update_option(self::IMPORT_URL, $url, true);
    }

    public function lowerProductsPerStep()
    {
        $productsPerStep = $this->getProductsPerStep();

        $response = $this->tryToLowerProductsPerStep($productsPerStep);

        if ($response === false) {
            $this->deleteLoweredProductsPerStep();
        }

        return $response;
    }


    /**
     * Update product properties by XML data
     */
    private function updateProduct($item): void
    {
        $exploded = explode('-', $item->ITEM_ID);
        if(isset($exploded[1])) {
            $combID = $exploded[1];
        }

        if(isset($combID)) {
            $subID = $combID;
            $product = wc_get_product((int) $subID);
            if($product && $product->exists()) {
                $product->set_regular_price((float)$item->PRICE);
                $product->save();
            }
        } else {
            $id = $item->ITEM_ID;
            $product = wc_get_product((int) $id);
            if($product && $product->exists()) {
                $product->set_regular_price((float) $item->PRICE);
                $product->save();
            }
        }
    }

    /**
     * Return value of lowered product step (repetitive call if 500 error timeout)
     */
    public function getLoweredProductsPerStep()
    {
        return (int)get_option(self::FEED_PRODUCTS_USER, 0);
    }

    public function setLoweredProductsPerStepAsMain()
    {
        $productsPerStep = $this->getLoweredProductsPerStep();
        $this->setProductsPerStep($productsPerStep);
        $this->deleteLoweredProductsPerStep();
    }

    /**
     * Return value of product per step
     * @return int
     */
    public function getProductsPerStep()
    {
        $loweredProductsPerStep = $this->getLoweredProductsPerStep();

        if ($loweredProductsPerStep != 0 && $loweredProductsPerStep !== '') {
            return $loweredProductsPerStep;
        }

        return (int)get_option(self::OPTIMIZATION_DB_NAME, 0);
    }

    public function setProductsPerStep($value): bool
    {
        return update_option(self::OPTIMIZATION_DB_NAME, $value, true);
    }

    public function deleteLoweredProductsPerStep(): bool
    {
        return update_option(self::FEED_PRODUCTS_USER, 0, true);
    }

    /**
     * Create necessary folders for importPrices
     */
    private function createTemporaryFolders(): void
    {
        DirectoryManager::createDir(__MERGADO_TMP_DIR__);
        DirectoryManager::createDir(__MERGADO_TMP_DIR__ . $this->currentBlogId);
        DirectoryManager::createDir($this->TMP_DIR_FOLDER);
    }

    public function getWizardData(): array
    {

        return [
            'token' => $this->token,
            'feed' => 'import',
            'cronAction' => 'importPrices',
            'ajaxGenerateAction' => 'ajax_generate_feed',
            'wpCronActive' => self::WP_CRON_ACTIVE_DB_NAME,
            'wpCronSchedule' => self::WP_CRON_SCHEDULE_DB_NAME,
            'wpCronFirst' => self::WP_CRON_START_DB_NAME,
            'cronUrl' =>  $this->getCronUrl(),
            'importUrl' => $this->getImportUrl(),
            'itemsPerStep' => $this->getProductsPerStep(),
            'settingsUrl' => admin_url('admin.php?page=mergado-feeds-other&mmp-tab=settings'),
            'percentage' => 'percentageToBeFilledFromSimpleXMl count'
        ];
    }

    public function getCronUrl(): string
    {
        return get_site_url() . '/mergado/?action=importPrices&token=' . $this->token;
    }

    public function isWpCronActive()
    {
        return get_option(self::WP_CRON_ACTIVE_DB_NAME, 0);
    }

    public function getCronSchedule()
    {
        return get_option(self::WP_CRON_SCHEDULE_DB_NAME, 0);
    }

    public function setLoweredProductsPerStep($loweredProductPerStepName, $value): bool
    {
        return update_option($loweredProductPerStepName, $value, true);
    }

    public function tryToLowerProductsPerStep($productsPerStep)
    {
        $loweredValue = round($productsPerStep / 2);

        if ($loweredValue < 10 && $loweredValue != 0) {
            return false;
        }

        if ($this->setLoweredProductsPerStep(self::FEED_PRODUCTS_USER, $loweredValue)) {
            return $loweredValue;
        }

        return false;
    }
}
