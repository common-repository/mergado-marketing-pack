<?php declare(strict_types=1);

namespace Mergado\Feed;

use Mergado\Helper\ResponseHelper;
use Mergado\Manager\DirectoryManager;
use Mergado\Manager\TokenManager;
use Mergado\Service\AlertService;
use Mergado\Service\Cron\CronScheduleService;
use Mergado\Service\LogService;
use Mergado\Utils\FileUtils;
use Mergado\Utils\MultisiteUtils;
use Mergado\Utils\TimeUtils;
use XMLWriter;

abstract class BaseFeed
{
    protected $logger;
    protected $token;
    protected $xmlOutputDir;
    protected $name;
    protected $tmpOutputDir;
    protected $lockOptionName;
    protected $currentBlogId;
    protected $tmpDir;
    protected $xmlDir;
    protected $totalItems;
    protected $feedSection;

    /**
     * DB NAME FOR: Current step of generation process
     * @var string
     */
    public $currentGenerationStepDbName;

    /**
     * DB NAME FOR: Total steps to generate feed (taken from last generation)
     * @var string
     */
    public $fullFeedStepCountDbName;

    /**
     * DB NAME FOR: Count of items per one step of generation selected by user
     * @var string
     */
    protected $userItemCountPerStepDbName;

    /**
     * DB NAME FOR: Lowered value of items per step when generation can't proceed
     * @var string
     */
    public $loweredItemCountPerStepDbName;

    /**
     * DB NAME FOR: Default number of items per step
     * @var string
     */
    public $defaultItemCountPerStepDbName;
    protected $wpCronScheduleDbName;
    protected $wpCronActiveDbName;
    public $wizardFinishedDbName;
    protected $feedVersion;
    private $wpCronStartDbName;
    private $defaultStepItems;

    protected $logContext;

    public function __construct(string $name, string $feedSection, int $totalItems, string $feedVersion, int $defaultStepItems)
    {
        $this->name = $name;
        $this->logContext = self::getLogContext();
        $this->currentGenerationStepDbName = self::getCurrentGenerationStepDbName();
        $this->fullFeedStepCountDbName = self::getFullFeedStepCountDbName();
        $this->loweredItemCountPerStepDbName = self::getLoweredItemCountPerStepDbName();
        $this->defaultItemCountPerStepDbName = self::getDefaultItemCountPerStepDbName();
        $this->wizardFinishedDbName = self::getWizardFinishedDbName();
        $this->userItemCountPerStepDbName = self::getUserItemCountPerStepDbName();
        $this->wpCronActiveDbName = self::getWpCronActiveDbName();
        $this->wpCronStartDbName = self::getWpCronStartDbName();
        $this->wpCronScheduleDbName = self::getWpCronScheduleDbName();
        $this->totalItems = $totalItems;
        $this->feedSection = $feedSection;
        $this->feedVersion = $feedVersion;
        $this->defaultStepItems = $defaultStepItems;

        $this->logger = LogService::getInstance();
        $this->token = TokenManager::getToken();
        $this->currentBlogId = MultisiteUtils::getCurrentBlogId();
        $this->lockOptionName = $name . '_feed_' . $this->token;

        $this->tmpDir = __MERGADO_TMP_DIR__ . $this->currentBlogId;
        $this->tmpOutputDir = __MERGADO_TMP_DIR__ . $this->currentBlogId . '/' . $name . 'Feed/';
        $this->xmlDir = __MERGADO_XML_DIR__ . $this->currentBlogId;
        $this->xmlOutputDir = __MERGADO_XML_DIR__ . $this->currentBlogId . '/';
    }

    /*******************************************************************************************************************
     * FEED LOCKING
     *******************************************************************************************************************/

    /**
     * Return if feed is currently locked
     *
     * @param $now
     * @return bool
     */
    protected function isFeedLocked($now): bool
    {
        $isLocked = get_option($this->lockOptionName);

        return $isLocked && $isLocked !== 0 && $isLocked >= $now;
    }

    /**
     * Lock feed
     * @param $now
     */
    protected function setFeedLocked($now): void
    {
        update_option($this->lockOptionName, $now->modify("+1 minute +30 seconds"), true);
        $this->logger->info('FEED LOCKED', $this->logContext);
    }

    /**
     * Unlock feed
     */
    protected function unlockFeed(): void
    {
        update_option($this->lockOptionName, 0, true);
        $this->logger->info('FEED UNLOCKED', $this->logContext);
    }

    /*******************************************************************************************************************
     * FEED TYPE CHECKS
     *******************************************************************************************************************/

    protected function isPartial($stepItems, $itemsList): bool
    {
        return $stepItems !== 0 && $itemsList !== [];
    }

    protected function isNormal($stepItems, $itemsList): bool
    {
        return $stepItems === 0 || $stepItems === false || ($itemsList && ($stepItems >= count($itemsList)));
    }

    /*******************************************************************************************************************
     * FEED URLS
     *******************************************************************************************************************/

    public function getFeedUrl(): string
    {
        return __MERGADO_XML_URL__ . $this->currentBlogId . '/' . $this->getFeedFileName();
    }

    public function getCronUrl(): string
    {
        return get_site_url() . '/mergado/?action=' . $this->name . 'Cron&token=' . $this->token;
    }

    /*******************************************************************************************************************
     * FEED PATH
     *******************************************************************************************************************/

    public function getFeedPath(): string
    {
        return wp_normalize_path(__MERGADO_XML_DIR__ . $this->currentBlogId . '/' . $this->getFeedFileName());
    }

    public function getFeedFileName() : string
    {
        return $this->name . '_' . $this->token . '.xml';
    }

    /*******************************************************************************************************************
     * FEED EXISTENCE
     *******************************************************************************************************************/

    public function isFeedExist(): bool
    {
        return file_exists($this->getFeedPath());
    }

    /*******************************************************************************************************************
     * FEED FILE/FOLDER MANIPULATION
     *******************************************************************************************************************/

    protected function createNecessaryDirs()
    {
        DirectoryManager::checkAndCreateTmpDataDir();
        DirectoryManager::createDir($this->tmpDir);
        DirectoryManager::createDir($this->tmpOutputDir);
        DirectoryManager::createDir($this->xmlDir);
        DirectoryManager::createDir($this->xmlOutputDir);
    }

    /**
     * Delete all files from TMP folder
     */
    public function deleteTemporaryFiles(): void
    {
        $files = glob($this->tmpOutputDir . '*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }


    /*******************************************************************************************************************
     * RESET
     *******************************************************************************************************************/

    /**
     * Reset feed and delete all TMP files
     */
    protected function resetFeedGenerating()
    {
        $this->resetFeedGenerationStep();
        $this->deleteTemporaryFiles();
    }

    /*******************************************************************************************************************
     * ITEMS PER STEP
     *******************************************************************************************************************/
    public function getStart($currentFilesCount = null) : int
    {
        if (!$currentFilesCount) {
            $currentFilesCount = $this->getCurrentTempFilesCount();
        }

        return $currentFilesCount === 0 ? 1 : $currentFilesCount + 1;
    }

    protected function getItemsPerStep(): int
    {
        $loweredItemsPerStep = $this->getLoweredItemsPerStep();

        if ($loweredItemsPerStep != 0) {
            return $loweredItemsPerStep;
        } else {
            return (int)get_option($this->userItemCountPerStepDbName, 0);
        }
    }

    public function getDefaultItemsPerStep(): int
    {
        $default = get_option($this->defaultItemCountPerStepDbName, false);

        if ($default === false) {
            return $this->defaultStepItems;
        }

        return (int) $default;
    }

    protected function setItemsPerStep($value): bool
    {
        return update_option($this->userItemCountPerStepDbName, $value, true);
    }

    public function setLoweredItemsPerStepAsMain()
    {
        $this->setItemsPerStep($this->getLoweredItemsPerStep());
        $this->deleteLoweredItemsPerStep();
    }

    public function setLowerItemsPerStep($value): bool
    {
        return update_option($this->loweredItemCountPerStepDbName, $value, true);
    }

    public function deleteLoweredItemsPerStep(): bool
    {
        return update_option($this->loweredItemCountPerStepDbName, 0, true);
    }

    /**
     * Return value of lowered items step (repetitive call if 500 error timeout)
     */
    public function getLoweredItemsPerStep() : int
    {
        return (int)get_option($this->loweredItemCountPerStepDbName, 0);
    }

    public function lowerItemsPerStep()
    {
        $itemsPerStep = $this->getItemsPerStep();

        $response = $this->getMagicWTF($this->loweredItemCountPerStepDbName, $itemsPerStep);

        if ($response === false) {
            $this->deleteLoweredItemsPerStep();
        }

        return $response;
    }

    protected function getMagicWTF($loweredItemsPerStepName, $itemsPerStep)
    {
        $loweredValue = round($itemsPerStep / 2);

        if ($loweredValue < 10 && $loweredValue != 0) {
            return false;
        }

        if (update_option($loweredItemsPerStepName, $loweredValue, true)) {
            return $loweredValue;
        }

        return false;
    }

    /*******************************************************************************************************************
     * FEED COUNT
     *******************************************************************************************************************/

    /**
     * @return int
     */
    protected function getCurrentTempFilesCount(): int
    {
        if (glob($this->tmpOutputDir . '*.xml')) {
            return count(glob($this->tmpOutputDir . '*.xml'));
        }

        return 0;
    }

    /**
     * Save the feed count to database for next runs
     */
    protected function saveFullFeedGenerationCount(): bool
    {
        return update_option($this->fullFeedStepCountDbName, (int)get_option($this->currentGenerationStepDbName), true);
    }

    protected function getFullFeedGenerationCount(): int
    {
        return (int)get_option($this->fullFeedStepCountDbName);
    }

    public function deleteFullFeedGenerationCount(): bool
    {
        return update_option($this->fullFeedStepCountDbName, 0, true);
    }

    /*******************************************************************************************************************
     * FEED ITERATOR
     *******************************************************************************************************************/

    protected function increaseGenerationStep(): bool
    {
        return update_option($this->currentGenerationStepDbName, (int)get_option($this->currentGenerationStepDbName) + 1, true);
    }

    public function setGenerationStep($value): bool
    {
        return update_option($this->currentGenerationStepDbName, $value, true);
    }

    protected function resetFeedGenerationStep(): bool
    {
        return update_option($this->currentGenerationStepDbName, 1, true);
    }

    /*******************************************************************************************************************
     * TIME CHECKERS
     *******************************************************************************************************************/

    public function getLastFeedChangeTimestamp()
    {
        $path = $this->getFeedPath();

        if (file_exists($path)) {
            $lastUpdate = filemtime($path);

            if ($lastUpdate) {
                return $lastUpdate;
            }
        }

        return false;
    }

    public function getLastFeedChange()
    {
        $path = $this->getFeedPath();

        $lastUpdate = FileUtils::getLastModified($path);

        if ($lastUpdate) {
            $lastUpdate = date(__('Y-m-d H:i', 'mergado-marketing-pack'), filemtime($path));
        } else {
            $lastUpdate = false;
        }

        return $lastUpdate;
    }

    /*******************************************************************************************************************
     * FEED FAILED CHECK
     *******************************************************************************************************************/

    public function hasFeedFailed(): bool
    {
        $alertService = AlertService::getInstance();
        $errors = $alertService->getFeedErrors($this->name);
        return in_array(AlertService::ALERT_NAMES['ERROR_DURING_GENERATION'], $errors);
    }


    /*******************************************************************************************************************
     * DOWNLOAD
     *******************************************************************************************************************/

    public function downloadFeed(string $missingFileRedirectUrl): void
    {
        $file = $this->getFeedPath();
        ResponseHelper::downloadFile($file, $missingFileRedirectUrl);
    }


    /*******************************************************************************************************************
     * CRONS
     *******************************************************************************************************************/
    public function getCronSchedule()
    {
        return get_option($this->wpCronScheduleDbName, 0);
    }

    /*******************************************************************************************************************
     * WIZARD
     *******************************************************************************************************************/

    public function isWizardFinished(): bool
    {
        return (bool)get_option($this->wizardFinishedDbName, 0);
    }

    public function isWpCronActive()
    {
        return get_option($this->wpCronActiveDbName, 0);
    }

    /*******************************************************************************************************************
     * PROGRESS - PERCENTAGES/ESTIMATES
     *******************************************************************************************************************/

    public function getFeedPercentage(): int
    {
        $itemsPerRun = $this->getItemsPerStep();
        $currentStep = $this->getCurrentTempFilesCount();
        $lastRunIterationCount = $this->getFullFeedGenerationCount();

        $totalFiles = $this->getTotalTemporaryFiles($itemsPerRun, $lastRunIterationCount);

        if ($totalFiles == 0) {
            return 0;
        }

        $finalPercentage = (int)round(($currentStep / ($totalFiles)) * 100);

        // Do not allow higher percentage than 100
        if ($finalPercentage > 100) {
            return 99;
        }

        return $finalPercentage;
    }

    public function getFeedEstimate($cronSchedule): string
    {
        $itemsPerStep = $this->getItemsPerStep();
        $itemsTotal = $this->totalItems;

        if ($itemsPerStep == 0 || trim((string)$itemsPerStep) === '') {
            $itemsPerStep = $itemsTotal;
        }

        if ($itemsPerStep === 0 && $itemsTotal === 0) {
            return TimeUtils::humanTimeDiff(1, 1); // Immediately
        } else {
            $numberOfRuns = $itemsTotal / $itemsPerStep;
            $scheduleSeconds = CronScheduleService::getScheduleInSeconds($cronSchedule);

            $totalTimeToGenerate = $numberOfRuns * $scheduleSeconds;

            if ($totalTimeToGenerate < $scheduleSeconds) {
                return TimeUtils::humanTimeDiff(0, $scheduleSeconds);
            } else {
                return TimeUtils::humanTimeDiff(0, $totalTimeToGenerate);
            }

        }
    }

    /*******************************************************************************************************************
     * MERGE FEEDS
     *******************************************************************************************************************/

    protected function mergeTemporaryFiles(): bool
    {
        $storage = $this->xmlOutputDir . $this->getFeedFileName();
        $tmpShopDir = $this->tmpOutputDir;

        $xmlWriter = new XMLWriter();
        $xmlWriter->openURI($storage);
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('CHANNEL');
        $xmlWriter->writeAttribute('xmlns', $this->feedVersion);

        $loop = 0;

        foreach (glob($tmpShopDir . '*.xml') as $file) {
            $xml = simplexml_load_file($file);
            $innerLoop = 0;

            foreach ($xml as $item) {
                if ($loop != 0 && (preg_match('/^mergado.woocommerce/', (string)$item[0]) || ($innerLoop == 0 || $innerLoop == 1))) {
                    $innerLoop++;
                    continue;
                } else {
                    $innerLoop++;
                    $xmlWriter->writeRaw($item->asXml());
                }
            }

            $loop++;
        }

        $xmlWriter->endElement();
        $xmlWriter->endDocument();

        $this->logger->info('Feed merged. XML created.', $this->logContext);

        $this->deleteTemporaryFiles();

        return true;
    }


    /*******************************************************************************************************************
     * DATA FOR TEMPLATES
     *******************************************************************************************************************/

    public function getDataForTemplates(): array
    {
        $feedUrl = $this->getFeedUrl();

        $alertService = AlertService::getInstance();
        $errors = $alertService->getFeedErrors($this->name);

        $feedExist = $this->isFeedExist();
        $percentage = $this->getFeedPercentage();

        if (!$feedExist && !$percentage) {
            $feedStatus = 'danger';
        } else if ($feedExist) {
            $feedStatus = 'success';
        } else {
            $feedStatus = 'warning';
        }

        return [
            'feedSection' => $this->feedSection,
            'feedName' => $this->name,
            'feedStatus' => $feedStatus,
            'feedExist' => $feedExist,
            'percentageStep' => $percentage,
            'feedUrl' => $feedUrl,
            'cronGenerateUrl' => get_site_url() . '/mergado/?action=' . $this->name . 'Cron&token=' . $this->token,
//            'wizardUrl' => '/wp-admin/admin.php?page=mergado-feeds-' . $this->feedSection . '&mmp-wizard=' . $this->name . ' &step=1' . '&mmp-tab=' . $this->name,
            'wizardUrl' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&mmp-wizard=' . $this->name . '&mmp-tab=' . $this->name),
            'deleteUrl' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&action=deleteFeed&feed=' . $this->name . '&token=' . $this->token . '&mmp-tab=' . $this->name),
            'downloadUrl' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&action=downloadFeed&feed=' . $this->name . '&token=' . $this->token . '&mmp-tab=' . $this->name),
            'generateUrl' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&mmp-wizard=' . $this->name . '&step=3&force=true' . '&mmp-tab=' . $this->name),
            'cronSetUpUrl' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&mmp-wizard=' . $this->name . '&step=4a&force=true' . '&mmp-tab=' . $this->name),
            'createExportInMergadoUrl' => 'https://app.mergado.com/new-project/prefill/?url=' . $feedUrl . '&inputFormat=mergado.cz.' . $this->name,
            'lastUpdate' => $this->getLastFeedChange(),
            'feedErrors' => $errors,
            'wizardCompleted' => $this->isWizardFinished()
        ];
    }

    public function getWizardData(): array
    {
        return [
            'feedSection' => $this->feedSection,
            'token' => $this->token,
            'feed' => $this->name,
            'cronAction' => $this->name . 'Cron',
            'ajaxGenerateAction' => 'ajax_generate_feed',
            'feedListLink' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&mmp-tab=' . $this->name),
            'wpCronActive' => $this->wpCronActiveDbName,
            'wpCronSchedule' => $this->wpCronScheduleDbName,
            'wpCronFirst' => $this->wpCronStartDbName,
            'cronUrl' => $this->getCronUrl(),
            'feedUrl' => $this->getFeedUrl(),
            'settingsUrl' => admin_url('admin.php?page=mergado-feeds-' . $this->feedSection . '&mmp-tab=settings'),
            'itemsPerStep' => $this->getItemsPerStep(),
            'percentage' => $this->getFeedPercentage(),
            'frontendData' => [
                'itemsPerStep' => $this->getItemsPerStep(),
                'feedRunning' => false,
                'feedFinished' => false,
            ]
        ];
    }

    public static function getFullFeedStepCountDbName(): string
    {
        return 'mergado-' . static::FEED_NAME . '-feed-count';
    }

    public static function getCurrentGenerationStepDbName(): string
    {
        return 'mergado-' . static::FEED_NAME . '-feed-iterator';
    }

    public static function getDefaultItemCountPerStepDbName(): string
    {
        return 'mergado-feed-' . static::FEED_NAME . '-default-step';
    }

    public static function getWizardFinishedDbName(): string
    {
        return 'mmp-wizard-finished-' . static::FEED_NAME;
    }

    //TODO: Naming of getLoweredItemCountPerStepDbName and getUserItemCountPerStepDbName variables is reversed
    public static function getLoweredItemCountPerStepDbName(): string
    {
        return 'mergado-feed-form-' . static::FEED_NAME . '-user';
    }

    //TODO: Naming of getLoweredItemCountPerStepDbName and getUserItemCountPerStepDbName variables is reversed
    public static function getUserItemCountPerStepDbName(): string
    {
        return 'feed-form-' . static::FEED_NAME;
    }

    public static function getWpCronActiveDbName(): string
    {
        return 'wp-cron-' . static::FEED_NAME . '-feed-active';
    }

    public static function getWpCronScheduleDbName(): string
    {
        return 'wp-cron-' . static::FEED_NAME . '-feed-schedule';
    }

    public static function getWpCronStartDbName(): string
    {
        return 'wp-cron-' . static::FEED_NAME . '-feed-start';
    }

    public function getTotalTemporaryFiles($itemsPerRun, $lastRunIterationCount)
    {
        if($itemsPerRun === 0) {
            $totalFiles = 0;
        } else {
            $totalItems = (int) $this->totalItems;

            $specialCoefficient = ( $lastRunIterationCount * $itemsPerRun ) * 1.2; // Last run items + 20%;

            // If first run of cron or someone added more than 20% of products
            if ( $lastRunIterationCount === 0 || $specialCoefficient < $totalItems ) {
                $totalGenerationRuns = $totalItems / $itemsPerRun;
            } else {
                $totalGenerationRuns = $lastRunIterationCount;
            }

            $totalFiles = ceil( $totalGenerationRuns );
        }

        return $totalFiles;
    }

    public static function getLogContext()
    {
        return 'feed-' . static::FEED_NAME;
    }
}
