<?php declare(strict_types=1);

namespace Mergado\Manager;

use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Helper\FieldSanitizationHelper;
use Mergado\Service\Cron\CronService;
use Mergado\Service\LogService;

class DatabaseManager
{
    public static function saveOptions(array $post, array $checkboxes = null, array $inputs = null, array $selectboxes = null): void
    {
        $logger = LogService::getInstance();

        // Log if changed
        if ($checkboxes !== null) {

            // Normalized array for sanitization purposes
            $normalizedCheckboxes = FieldSanitizationHelper::normalizeInputs($checkboxes, FieldSanitizationHelper::FIELD_TYPE_TOGGLE);

            foreach ($normalizedCheckboxes as $fieldName => $filter) {

                // DB value
                $currentFieldValue = get_option($fieldName);

                // Proceed if input data came in post
                if (isset($post[$fieldName])) {

                    // New sanitized value
                    $sanitizedNewFieldValue = FieldSanitizationHelper::sanitizeByType($post[$fieldName], $filter);

                    // Log changes
                    if (!$currentFieldValue && $sanitizedNewFieldValue === 1) {
                        $logger->info($fieldName . ' changed - from ' . FieldSanitizationHelper::sanitizeToggle($currentFieldValue) . ' to ON', 'settings');
                    }

                    // Save to DB
                    update_option($fieldName, 1, true);

                    // Schedule or remove cron from wp_cron
                    if (strpos($fieldName, 'wp-cron-') !== false && strpos($fieldName, '-active') !== false) {
                        $name = explode('-active', $fieldName)[0];

                        CronService::addTask($name, $post[$name . '-schedule'], '');
                    }
                } elseif ($currentFieldValue === '1') {
                    $logger->info($fieldName . ' changed from ' . FieldSanitizationHelper::sanitizeToggle($currentFieldValue) . ' to OFF', 'settings');

                    // Save to DB
                    update_option($fieldName, 0, true);

                    // Remove cron from wp_cron
                    if (strpos($fieldName, 'wp-cron-') !== false && strpos($fieldName, '-active') !== false) {
                        $name = explode('-active', $fieldName)[0];

                        CronService::removeTask($name);
                    }
                }
            }
        }

        if ($inputs !== null) {

            // Normalized array for sanitization purposes
            $normalizedInputs = FieldSanitizationHelper::normalizeInputs($inputs, FieldSanitizationHelper::FIELD_TYPE_TEXT);

            foreach ( $normalizedInputs as $fieldName => $filter) {

                // Proceed if input data came in post
                if (isset($post[$fieldName])) {

                    // Default and new value
                    $currentFieldValue = get_option($fieldName);
                    $sanitizedNewFieldValue = FieldSanitizationHelper::sanitizeByType($post[$fieldName], $filter);

                    // Log if changed number of items in one feed run + remove generated files
                    if ($currentFieldValue !== $sanitizedNewFieldValue) {
                        $logger->info($fieldName . ' changed from ' . $currentFieldValue . ' to ' . $sanitizedNewFieldValue, 'settings');

                        if ($fieldName === ProductFeed::getUserItemCountPerStepDbName()) {
                            $productFeed = new ProductFeed();
                            $productFeed->deleteFullFeedGenerationCount();
                            $productFeed->deleteTemporaryFiles();
                        } else if ($fieldName === CategoryFeed::getUserItemCountPerStepDbName()) {
                            $categoryFeed = new CategoryFeed();
                            $categoryFeed->deleteFullFeedGenerationCount();
                            $categoryFeed->deleteTemporaryFiles();
                        } else if ($fieldName === StockFeed::getUserItemCountPerStepDbName()) {
                            $stockFeed = new StockFeed();
                            $stockFeed->deleteFullFeedGenerationCount();
                            $stockFeed->deleteTemporaryFiles();
                        } else if ($fieldName === CustomerFeed::getUserItemCountPerStepDbName()) {
                            $customerFeed = new CustomerFeed();
                            $customerFeed->deleteFullFeedGenerationCount();
                            $customerFeed->deleteTemporaryFiles();
                        }
                    }

                    // Save to DB
                    update_option($fieldName, $sanitizedNewFieldValue, true);
                }
            }
        }

        if ($selectboxes !== null) {

            // Normalized array for sanitization purposes
            $normalizedSelectBoxes = FieldSanitizationHelper::normalizeInputs($selectboxes, FieldSanitizationHelper::FIELD_TYPE_TEXT);

            foreach ($normalizedSelectBoxes as $fieldName => $filter) {

                // Proceed if input data came in post
                if (isset($post[$fieldName])) {

                    // Default and new value
                    $currentFieldValue = get_option($fieldName);
                    $sanitizedNewFieldValue = FieldSanitizationHelper::sanitizeByType($post[$fieldName], $filter);

                    // Log if changed
                    if ($currentFieldValue !== $sanitizedNewFieldValue) {
                        $logger->info($fieldName . ' changed from ' . $currentFieldValue .' to ' . $sanitizedNewFieldValue, 'settings');
                    }

                    update_option($fieldName, $sanitizedNewFieldValue, true);
                }
            }
        }
    }
}
