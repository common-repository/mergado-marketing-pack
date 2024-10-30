<?php

use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Manager\DatabaseManager;
use Mergado\Service\Cron\CronService;
use Mergado\Service\ProductPriceImportService;
use Mergado\FeedVisibility\OtherFeedVisibilityService;

$otherFeedService = OtherFeedVisibilityService::getInstance();

/**
 * Product feed form settings
 */


if (isset($_POST['submit-settings-form'])) {
    //Feed optimization
    DatabaseManager::saveOptions($_POST, [], [StockFeed::getUserItemCountPerStepDbName()]);
    DatabaseManager::saveOptions($_POST, [], [CategoryFeed::getUserItemCountPerStepDbName()]);
    DatabaseManager::saveOptions($_POST, [], [CustomerFeed::getUserItemCountPerStepDbName()]);

    //Import settings
    DatabaseManager::saveOptions($_POST, [], [ProductPriceImportService::OPTIMIZATION_DB_NAME]);
    DatabaseManager::saveOptions($_POST, [], [ProductPriceImportService::IMPORT_URL]);

	// WP CRON FORCED options - TAKE CARE ON TWO LOCATIONS !!
    DatabaseManager::saveOptions($_POST, [CronService::FORCE_ENABLE_CRON_DB_NAME]);

    //Visibility fields
    $otherFeedService::saveFields($_POST);

    //Remove wizard params from URL
    wp_redirect('admin.php?page=' . $_GET['page'] . '&mmp-tab=' . $_GET['mmp-tab'] . '&flash=settingsSaved');
}
?>


<div class="card full">
    <form method="post" id="settings-form" action="">
        <h1 class="mmp_h1"><?php echo __('Global settings for all other feeds', 'mergado-marketing-pack') ?></h1>

        <?php
            $alertData = ['alertSection' => 'other', 'feedName' => 'other'];

            include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/settingsInfo.php';
        ?>

        <div class="mmp_settingsBox">
            <table class="">
                <tbody>
                <tr>
                    <th style="vertical-align: baseline;">
                        <label data-tippy-content="<?php echo __('Select which products to export according to Visibility in WooCommerce. The Visibility value will be identified in the <code><&#8203;VISIBILITY&#8203;></code> element.', 'mergado-marketing-pack') ?>"
                               for="feed-form-vat"><?php echo __('Export by Visibility', 'mergado-marketing-pack') ?></label>
                    </th>
                    <td style="text-align: left;">
                        <?php foreach($otherFeedService->baseVisibilityOptions as $item):?>
                            <div class="mmp_checkbox--inline">
                                <input type="checkbox"
                                       id="<?php echo $item['databaseName'] ?>"
                                       name="<?php echo $item['databaseName'] ?>"
                                       <?php if ($item['value'] === '1'){ ?>checked="checked"<?php } ?>>

                                <label for="<?php echo $item['databaseName'] ?>">
                                    <?php echo $item['name']; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="mmp_settingsBox">
            <table class="">
                <tbody>
                <tr>
                    <th style="vertical-align: baseline;">
                        <label data-tippy-content="<?php echo __('Select which products to export according to Catalog visibility in WooCommerce. The Visibility value will be identified in the <code><&#8203;CATALOG_VISIBILITY&#8203;></code> element.', 'mergado-marketing-pack') ?>"
                               for="feed-form-vat"><?php echo __('Export by Catalog visibility', 'mergado-marketing-pack') ?></label>
                    </th>
                    <td style="text-align: left;">
                        <?php foreach($otherFeedService->catalogVisibilityOptions as $item):?>
                            <div class="mmp_checkbox--inline">
                                <input type="checkbox"
                                       id="<?php echo $item['databaseName'] ?>"
                                       name="<?php echo $item['databaseName'] ?>"
                                       <?php if ($item['value'] === '1'){ ?>checked="checked"<?php } ?>>

                                <label for="<?php echo $item['databaseName'] ?>">
                                    <?php echo $item['name']; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="mmp_advancedSettingsBox">
            <div class="mmp_advancedSettingsBox__header">
                <p><?php echo __('Advanced settings', 'mergado-marketing-pack') ?></p>
                <div class="mmp_advancedSettingsBox__toggler">
                    <svg>
                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#chevron-down' ?>"></use>
                    </svg>
                </div>
            </div>
            <div class="mmp_advancedSettingsBox__content">

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
			            <?php echo __('Batch export Category feeds', 'mergado-marketing-pack') ?>
                    </div>

                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-category"
                                       data-tippy-content="<?php echo __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 3000 items per batch step.', 'mergado-marketing-pack') ?>">
						            <?php echo __('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;"><input type="number" min="1" id="feed-form-category" name="feed-form-category"
                                                                 placeholder="<?php echo __('Insert number of products', 'mergado-marketing-pack') ?>"
                                                                 value="<?php echo get_option('feed-form-category', ''); ?>">
                                <br>
                                <small><?php echo __('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
                        <?php echo __('Batch export Customer feeds', 'mergado-marketing-pack') ?>
                    </div>

                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-customer"
                                       data-tippy-content="<?php echo __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 3000 items per batch step.', 'mergado-marketing-pack') ?>">
                                    <?php echo __('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;"><input type="number" min="1" id="feed-form-customer" name="feed-form-customer"
                                                                 placeholder="<?php echo __('Insert number of customers', 'mergado-marketing-pack') ?>"
                                                                 value="<?php echo get_option('feed-form-customer', ''); ?>">
                                <br>
                                <small><?php echo __('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
			            <?php echo __('Batch export Heureka Availability feeds', 'mergado-marketing-pack') ?>
                    </div>
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-stock"
                                       data-tippy-content="<?php echo __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 5000 items per batch step.', 'mergado-marketing-pack') ?>">
						            <?php echo __('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;"><input type="number" min="1" id="feed-form-stock" name="feed-form-stock"
                                                                 placeholder="<?php echo __('Insert number of products', 'mergado-marketing-pack') ?>"
                                                                 value="<?php echo get_option('feed-form-stock', ''); ?>">
                                <br>
                                <small><?php echo __('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <div class="mmp_settingsBox__top">
			            <?php echo __('Batch Import prices feeds', 'mergado-marketing-pack') ?>
                    </div>
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="import-form-products"
                                       data-tippy-content="<?php echo __('Changing the batch size could seriously effect the performance of your website. We advice against changing the batch size if you are unsure about its effects!<br><br>Default number is set to 3000 items per batch step.', 'mergado-marketing-pack') ?>">
						            <?php echo __('Change the number of products per batch (Change only if advised by our support team)', 'mergado-marketing-pack') ?>
                                </label>
                            </th>
                            <td class="text-align: left;">
                                <input type="number" min="1" id="import-form-products" name="import-form-products"
                                       placeholder="<?php echo __('Insert number of steps', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('import-form-products', ''); ?>">
                                <br>
                                <small><?php echo __('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mmp_settingsBox">
                    <table>
                        <tbody>
                        <tr>
                            <th>
                                <label for="<?php echo CronService::FORCE_ENABLE_CRON_DB_NAME ?>"
                                       data-tippy-content="<?php echo __('Checking this checkbox will unlock the WP cron wizard even with:<br><br> <strong>DISABLED_WP_CRON = true in wp-config.php</strong><br><br> This feature is intended to be used with externaly enabled WP CRON script.', 'mergado-marketing-pack') ?>"
                                ><?php echo __('Unlock WP cron forms', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td class="text-align: left;">
                                <input type="checkbox"
                                       id="<?php echo CronService::FORCE_ENABLE_CRON_DB_NAME ?>"
                                       name="<?php echo CronService::FORCE_ENABLE_CRON_DB_NAME ?>"
							           <?php if (CronService::isWpCronForceEnabled()){ ?>checked="checked"<?php } ?>>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="mmp_btnHolder mmp_btnHolder--right">
            <input type="submit" class="button mmp_btn__blue mmp_btn--wide"
                   value="<?php echo __('Save', 'mergado-marketing-pack') ?>" name="submit-settings-form">
        </div>
    </form>
</div>
