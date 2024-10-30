<?php if (@$wizardType === $wizardData['feed'] && $wizardStep === '4b'): ?>
    <div class="mmp_wizard active" data-mmp-wizard-step="4b" data-mmp-wizard-type="<?php echo $wizardData['feed'] ?>">
<?php else: ?>
    <div class="mmp_wizard" data-mmp-wizard-step="4b" data-mmp-wizard-type="<?php echo $wizardData['feed'] ?>">
<?php endif ?>
    <div class="card full">
        <div class="mmp_wizard__content">
            <h1 class="mmp_wizard__heading"><?php echo __('Dont\'t forget to set up your task scheduler - CRON', 'mergado-marketing-pack'); ?></h1>

            <?php
                $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/cronInfo.php';
            ?>

            <div class="mmp_wizard__content_body">
                <div class="mmp_wizard__content_heading">
                    <span><?php echo __('How to set up your task scheduler for automatic feed updates', 'mergado-marketing-pack'); ?></span>
                    <a target="_blank" href="<?php echo __('https://www.mergado.com/automation-with-crons', 'mergado-marketing-pack');?>"><?php echo __('Read our article about webcron and what it is for. ', 'mergado-marketing-pack'); ?></a>
                </div>

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
                            <p><?php echo __('Each time cron calls a cron URL, feed generation is started. This will keep your XML feed up to date.', 'mergado-marketing-pack'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mmp_wizard__cronLink">
                    <div class="mmp_wizard__cron_left">
                        <div class="mmp_wizard__cronLink_name"><?php echo __('Cron URL', 'mergado-marketing-pack'); ?></div>
<!--                        <div class="mmp_wizard__cronLink_link">--><?//= $wizardData['cronUrl'] ?><!--</div>-->
                        <input type="text" class="mmp_feedBox__url mmp_wizard__cronLink_link" readonly value="<?php echo $wizardData['cronUrl'] ?>">
                    </div>
                    <div class="mmp_wizard__cronLink_copy"><a class="mmp_btn__blue--small" data-copy-stash="<?php echo $wizardData['cronUrl']?>" href="javascript:void(0);"><?php echo __('Copy cron URL', 'mergado-marketing-pack'); ?></a></div>
                </div>
            </div>

            <?php if($wizardForced): ?>
                <div class="mmp_wizard__buttons">
                    <a href="javascript:void(0);" class="mmp_btn__white" data-mmp-wizard-go="4a"><?php echo __('Back', 'mergado-marketing-pack') ?></a>
                    <a href="<?php echo $wizardData['feedListLink'] ?>" class="mmp_btn__blue"><?php echo __('Go to list of feeds', 'mergado-marketing-pack') ?></a>
                </div>
            <?php else: ?>
                <div class="mmp_wizard__buttons">
                    <a href="javascript:void(0);" class="mmp_btn__white" data-mmp-wizard-go="4a"><?php echo __('Back', 'mergado-marketing-pack') ?></a>
                    <a href="javascript:void(0);" class="mmp_btn__blue" data-mmp-wizard-go="" data-mmp-wizard-do-before="setWizardCompletedAndGo" data-go-to-link="<?php echo $wizardData['feedListLink'] ?>"><?php echo __('Continue to list of feeds', 'mergado-marketing-pack'); ?></a>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

