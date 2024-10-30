<?php

use Mergado\Manager\DatabaseManager;
use Mergado\Service\Cron\CronScheduleService;
use Mergado\Service\Cron\CronService;
use Mergado\Service\ProductPriceImportService;

$productPriceImportService = ProductPriceImportService::getInstance();
$wizardData = $productPriceImportService->getWizardData();

/**
 * Cron settings
 */
if (isset($_POST["submit-import-form"])) {
    DatabaseManager::saveOptions($_POST, [
        ProductPriceImportService::WP_CRON_ACTIVE_DB_NAME,
    ], [
        ProductPriceImportService::WP_CRON_SCHEDULE_DB_NAME,
        ProductPriceImportService::WP_CRON_START_DB_NAME,
        ProductPriceImportService::IMPORT_URL
    ]);
}
?>

<div class="card full">
    <form id="import-form" method="post" class="width_800">
        <h1 class="mmp_h1"><?php echo __('Update product prices using an XML file', 'mergado-marketing-pack') ?></h1>


        <div class="mmp_wizard__content_body">
            <div class="mmp_wizard__content_heading">
                <?php echo __('Insert URL of XML price import feed from Mergado App', 'mergado-marketing-pack'); ?>
            </div>

            <label for="<?php echo ProductPriceImportService::IMPORT_URL ?>" class="priceImportLabel">
                <div class="priceImportLabel__bottom">
                    <input type="url" value="<?php echo $productPriceImportService->getImportUrl() ?>" placeholder="<?php echo __('Insert price import URL from our Mergado App', 'mergado-marketing-pack') ?>" id="<?php echo ProductPriceImportService::IMPORT_URL ?>" name="<?php echo ProductPriceImportService::IMPORT_URL ?>">
                    <a href="#" class="saveAndImportRecursive mmp_btn__blue mmp_btn__blue--small" data-feed="importPrices" data-token="<?php echo $token ?>">
                        <svg>
                            <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#turn-on' ?>"></use>
                        </svg>

                        <span><?php echo __('Save and start import', 'mergado-marketing-pack'); ?></span>
                    </a>
                </div>
            </label>
        </div>

        <div class="mmp_wizard__content_body">
            <div class="mmp_wizard__content_heading">
                <?php echo __('Set Cron for periodically downloading an XML file.', 'mergado-marketing-pack'); ?>
            </div>

            <div class="importTabs" data-import-tab="1">
	            <?php
	            if ( !defined( 'DISABLE_WP_CRON' ) || DISABLE_WP_CRON == false || CronService::isWpCronForceEnabled()):
	            ?>

                    <div class="mmp_wizard__wp_cron">
                        <div>
                            <div class="mmp_wizard__wp_cron_heading">
                                <svg>
                                    <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#wp-logo' ?>"></use>
                                </svg>
                                <h3>
                                    <?php echo __('Activate WP cron', 'mergado-marketing-pack') ?></h3></div>
                            <div>
                                <input type="checkbox" id="<?php echo $wizardData['wpCronActive'] ?>" name="<?php echo $wizardData['wpCronActive'] ?>"
                                       <?php if (get_option($wizardData['wpCronActive'], 0) == 1){ ?>checked="checked"<?php } ?>
                                >
                            </div>
                        </div>
                        <div>
                            <div class="mmp_wizard__wp_cron_heading">
                                <svg>
                                    <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#refresh' ?>"></use>
                                </svg>
                                <h3><?php echo __('Cron schedule', 'mergado-marketing-pack') ?></h3>
                            </div>
                            <div class="mmp_cron__feed_schedule">
                                <select name="<?php echo $wizardData['wpCronSchedule'] ?>" id="<?php echo $wizardData['wpCronSchedule'] ?>">

                                    <?php foreach(CronScheduleService::getScheduleTasks() as $key => $item) : ?>

                                        <option value="<?php echo $key ?>"
                                            <?php if($key === get_option($wizardData['wpCronSchedule'], 0)): ?> selected <?php endif ?>
                                        >
                                            <?php echo $item ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
<!--                        <div>-->
<!--                            <div class="mmp_wizard__wp_cron_heading">-->
<!--                                <svg  style="height: 21px;">-->
<!--                                    <use xlink:href="--><?//= plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#in-progress' ?><!--"></use>-->
<!--                                </svg>-->
<!--                                <h3>--><?php //_e('First start', 'mergado-marketing-pack') ?><!--</h3></div>-->
<!--                            <div><input type="time" name="--><?//= $wizardData['wpCronFirst'] ?><!--" id="--><?//= $wizardData['wpCronFirst'] ?><!--" value="--><?//= get_option($wizardData['wpCronFirst'], 0) ?><!--" pattern="(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]" title='"XX:XX"'/></div>-->
<!--                        </div>-->
                        <input type="hidden" name="token" id="token" value="<?php echo $wizardData['token'] ?>" />
                        <input type="hidden" name="feed" id="feed" value="<?php echo $wizardData['feed']  ?>" />
                        <input type="hidden" name="action" id="action" value="ajax_save_wp_cron" />
                    </div>

                    <div class="mmp_wizard__divider">
                        <?php echo __('OR', 'mergado-marketing-pack'); ?>
                    </div>
                    <div class="mmp_wizard__bottom">
                        <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                           data-mmp-import-tab="2"><?php echo __('Set up an external cron service. Click here.', 'mergado-marketing-pack'); ?></a>
                    </div>
                <?php else: ?>
                        <div class="mmp_wizard__wpCronDisabled">
                            <h3 class="mmp_wizard__wpCronDisabled_title">
                                <svg>
                                    <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#wp-logo' ?>"></use>
                                </svg>

			                    <?php echo __('Your wp cron (wp-cron.php) is disabled.', 'mergado-marketing-pack'); ?></h3>
                            <p class="mmp_wizard__wpCronDisabled_text"><?php echo __('You must use an external cron service or enable<br> the WP cron function in the wp-config.php file.', 'mergado-marketing-pack') ?>
                            <br>
                            <a href="<?php echo $wizardData['settingsUrl'] ?>"><small>For developers: If you use external service to start WP CRON, click here to activate</small></a></p>
                            <hr>
                            <div class="mmp_wizard__wpCronDisabled_link">
                                <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                                   data-mmp-import-tab="2"><?php echo __('Set up an external cron service. Click here.', 'mergado-marketing-pack'); ?></a>
                            </div>

                        </div>
                <?php endif; ?>
            </div>

            <div class="importTabs" data-import-tab="2" style="display: none;">
                <div class="mmp_wizard__cron">
                    <div class="mmp_wizard__cronItem mmp_wizard__cronItem--first">
                        <h2 class="mmp_wizard__cronItemTop">
                            <?php echo __('Open your task scheduler - Webcron', 'mergado-marketing-pack'); ?>
                            <svg>
                                <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#open' ?>"></use>
                            </svg>
                        </h2>
                        <p><?php echo __('Usually cron service is available as part of hosting or you can use an external service.', 'mergado-marketing-pack'); ?></p>
                    </div>

                    <div class="mmp_wizard__cronArrow">
                        <svg>
                            <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#arrow-right' ?>"></use>
                        </svg>
                    </div>

                    <div class="mmp_wizard__cronItem mmp_wizard__cronItem--second">
                        <h2 class="mmp_wizard__cronItemTop">
                            <?php echo __('Enter the cron URL and set the time', 'mergado-marketing-pack'); ?>
                            <svg>
                                <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#in-progress' ?>"></use>
                            </svg>
                        </h2>
                        <p><?php echo __('Cron will automatically  call the URL at the intervals you specify (eg every hour).', 'mergado-marketing-pack'); ?></p>
                    </div>

                    <div class="mmp_wizard__cronArrow">
                        <svg>
                            <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#arrow-right' ?>"></use>
                        </svg>
                    </div>

                    <div class="mmp_wizard__cronItem mmp_wizard__cronItem--third">
                        <div>
                            <h2 class="mmp_wizard__cronItemTop">
                                <?php echo __('The feed will update automatically', 'mergado-marketing-pack'); ?>
                                <svg>
                                    <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#refresh' ?>"></use>
                                </svg>
                            </h2>
                            <p><?php echo __('Each time cron calls a cron URL, import run starts. This will keep your product prices up to date.', 'mergado-marketing-pack'); ?></p>
                        </div>
                    </div>
                </div>

	            <?php
	            if ( !defined( 'DISABLE_WP_CRON' ) || DISABLE_WP_CRON == false || CronService::isWpCronForceEnabled()):
	            ?>
                    <div class="mmp_wizard__divider">
                        <?php echo __('OR', 'mergado-marketing-pack'); ?>
                    </div>
                    <div class="mmp_wizard__bottom">
                        <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                           data-mmp-import-tab="1"><?php echo __('Set up WordPress cron service. Click here', 'mergado-marketing-pack'); ?></a>
                    </div>

                <?php endif; ?>
            </div>

            <div class="mmp_feedBox__line mmp_mt-30">
                <div class="mmp_feedBox__line--left">
                    <p class="mmp_feedBox__name"><?php echo __('Import cron URL', 'mergado-marketing-pack') ?></p>
                    <input type="text" class="mmp_feedBox__url" readonly value="<?php echo $wizardData['cronUrl'] ?>">
                </div>
                <a class="mmp_feedBox__button mmp_btn__blue mmp_btn__blue--small mmp_feedBox__copy priceImport__copy"
                   data-copy-stash="<?php echo $wizardData['cronUrl'] ?>"
                   href="javascript:void(0);">

                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#copy' ?>"></use>
                    </svg>
                    <?php echo __('Copy cron URL') ?>
                </a>
            </div>
        </div>

        <div class="mmp_btnHolder mmp_btnHolder--right">
            <input type="submit" form="import-form" class="button mmp_btn__blue mmp_btn--wide" value="Save" name="submit-import-form">
        </div>
    </form>
</div>

<script>
  // Tab switching
  document.addEventListener('DOMContentLoaded', function () {
    var $ = jQuery;
    $('[data-mmp-import-tab]').on('click', function () {
      var val = $(this).attr('data-mmp-import-tab');
      $('[data-import-tab]').hide();
      $('[data-import-tab="' + val + '"]').show();
    });
  });
</script>
