<?php

use Mergado\Service\Cron\CronScheduleService;
use Mergado\Service\Cron\CronService;

?>

<?php if (@$wizardType === $wizardData['feed'] && $wizardStep === '4a'): ?>
    <div class="mmp_wizard active" data-mmp-wizard-step="4a" data-mmp-wizard-type="<?php echo $wizardData['feed'] ?>">
<?php else: ?>
    <div class="mmp_wizard" data-mmp-wizard-step="4a" data-mmp-wizard-type="<?php echo $wizardData['feed'] ?>">
<?php endif ?>

    <div class="card full">
        <div class="mmp_wizard__content">
            <h1 class="mmp_wizard__heading"><?php echo __('Set up refresh interval - CRON', 'mergado-marketing-pack'); ?></h1>

            <?php
                $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/cronInfo.php';
            ?>

            <div class="mmp_wizard__content_body">

	            <?php
                    if ( !defined( 'DISABLE_WP_CRON' ) || DISABLE_WP_CRON == false || CronService::isWpCronForceEnabled()):
                ?>

                    <div class="mmp_wizard__content_heading">
                        <?php echo __('Set up a feed refresh using the WordPress cron task scheduler.', 'mergado-marketing-pack'); ?>
                    </div>

                    <form class="mmp_wizard__wp_cron">
                        <div>
                            <div class="mmp_wizard__wp_cron_heading">
                                <svg>
                                    <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#wp-logo' ?>"></use>
                                </svg>
                                <h3>
                                    <?php echo __('Activate WP cron', 'mergado-marketing-pack') ?></h3></div>
                            <div>
                                <input type="checkbox" id="<?= $wizardData['wpCronActive'] ?>" name="<?= $wizardData['wpCronActive'] ?>"
                                       <?php if (get_option($wizardData['wpCronActive'], 1) == 1){ ?>checked="checked"<?php } ?>
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
                                <select class="wp-schedule-input" name="<?php echo $wizardData['wpCronSchedule'] ?>" id="<?php echo $wizardData['wpCronSchedule'] ?>" data-mmp-wizard-type="<?php echo $wizardData['feed']?>">

                                    <?php foreach(CronScheduleService::getScheduleTasks() as $key => $item) : ?>

                                        <option value="<?php echo $key ?>"
                                            <?php if($key === get_option($wizardData['wpCronSchedule'], 0)): ?> selected <?php endif ?>
                                        >
                                            <?php echo $item ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <span class="mmp_cron__feed_estimate">
                                    <?php echo __('The entire feed will be generated in approximately ', 'mergado-marketing-pack') . '<strong data-pps-output=""></strong>' .  '.'?>
                                </span>
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
                    </form>


                    <div class="mmp_wizard__divider">
                        <?php echo __('OR', 'mergado-marketing-pack'); ?>
                    </div>
                    <div class="mmp_wizard__bottom">
                        <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                           data-mmp-wizard-go="4b"><?php echo __('Set up an external cron service. Click here.', 'mergado-marketing-pack'); ?></a>
                    </div>


                <?php else: ?>
                    <div class="mmp_wizard__wpCronDisabled">
                        <h3 class="mmp_wizard__wpCronDisabled_title">
                            <svg>
                                <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#wp-logo' ?>"></use>
                            </svg>

                            <?php echo __('Your wp cron (wp-cron.php) is disabled.', 'mergado-marketing-pack'); ?></h3>
                        <p class="mmp_wizard__wpCronDisabled_text"><?php echo __('You must use an external cron service or enable<br> the WP cron function in the wp-config.php file.', 'mergado-marketing-pack') ?><br>
                        <a href="<?php echo $wizardData['settingsUrl'] ?>"><small>For developers: If you use external service to start WP CRON, click here to activate</small></a></p>
                        <hr>
                        <div class="mmp_wizard__wpCronDisabled_link">
                            <a href="javascript:void(0);" class="mmp_btn__white mmp_btn__white--lowercase"
                               data-mmp-wizard-go="4b"><?php echo __('Set up an external cron service. Click here.', 'mergado-marketing-pack'); ?></a>
                        </div>

                    </div>
	            <?php endif; ?>
            </div>

            <?php if($wizardForced): ?>
                <div class="mmp_wizard__buttons mmp_justify_end">
                    <a href="javascript:void(0);" class="mmp_btn__blue"
                       data-mmp-wizard-go="4a" data-mmp-wizard-do-before="mmpSaveWpCronAndGo" data-go-to-link="<?php echo $wizardData['feedListLink'] ?>"><?php echo __('Save and go to list of feeds', 'mergado-marketing-pack'); ?></a>
                </div>
            <?php else: ?>
                <div class="mmp_wizard__buttons mmp_justify_end">
<!--                    <a href="javascript:void(0);" class="mmp_btn__white"-->
<!--                       data-mmp-wizard-go="3" data-mmp-wizard-do-before="mmpStartWizardGenerating">--><?php //_e('Back', 'mergado-marketing-pack'); ?><!--</a>-->
                    <a href="javascript:void(0);" class="mmp_btn__blue"
                       data-mmp-wizard-go="4a" data-mmp-wizard-do-before="mmpSaveWpCronAndGo" data-go-to-link="<?php echo $wizardData['feedListLink'] ?>"><?php echo __('Save and go to list of feeds', 'mergado-marketing-pack'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
