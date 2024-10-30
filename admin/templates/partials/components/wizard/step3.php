<?php
   if(($isAlreadyFinished && !$wizardStep) || ($wizardStep == 3 && $wizardForced)):
?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
          var $ = jQuery;

          if ($('[data-mmp-tab-button="<?php echo $wizardData['feed'] ?>"]').closest('li').hasClass('active')) {
              window.mmpWizard.startFeedGenerating('<?php echo $wizardData['feed'] ?>');
          }
        });
    </script>
<?php
    endif;
?>

<?php if ((@$wizardType === $wizardData['feed'] && $wizardStep === '3') || ($isAlreadyFinished && !$wizardStep)): ?>
<div class="mmp_wizard active" data-mmp-wizard-step="3" data-mmp-wizard-type="<?php echo $wizardData['feed'] ?>">
    <?php else: ?>
    <div class="mmp_wizard" data-mmp-wizard-step="3" data-mmp-wizard-type="<?php echo $wizardData['feed'] ?>">
        <?php endif ?>
        <div class="card full">
            <div class="mmp_wizard__content">

                <?php if ($wizardForced): ?>
                    <h1 class="mmp_wizard__heading" data-feed-finished="false"><?php echo sprintf(__('Wait until your %s feed is created', 'mergado-marketing-pack'), $wizardName); ?></h1>
                    <h1 class="mmp_wizard__heading" data-feed-finished="true"><?php echo sprintf(__('Your %s feed is ready', 'mergado-marketing-pack'), $wizardName); ?></h1>
                <?php else: ?>
                    <h1 class="mmp_wizard__heading" data-feed-finished="false"><?php echo sprintf(__('Wait until your first %s feed is created', 'mergado-marketing-pack'), $wizardName); ?></h1>
                    <h1 class="mmp_wizard__heading" data-feed-finished="true"><?php echo sprintf(__('Your first %s feed is ready.', 'mergado-marketing-pack'), $wizardName); ?></h1>
                <?php endif; ?>

                <div data-feed-finished="false">
                    <?php
                    $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                    include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/longTime.php';
                    ?>
                </div>

                <div data-feed-finished="true">
                    <?php
                    $alertData = ['alertSection' => $wizardData['feedSection'], 'feedName' => $wizardData['feed']];

                    if ($wizardData['feed'] === 'stock') {
                        include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedIsReadyStock.php';
                    } else {
                        include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedIsReady.php';
                    }
                    ?>
                </div>

                <div class="mmp_wizard__content">
                    <div class="mmp_wizard__content_body">
                        <div class="mmp_wizard__generate">
                            <form>
                                <div class="mmp_wizard__generating" data-status="inactive">
                                    <div class="mmp_wizard__generating_status">

                                    </div>

                                    <div style="position: relative;"
                                         class="rangeSlider rangeSlider-<?php echo $wizardData['feed'] ?>"
                                         data-range-index="<?php echo $wizardData['feed'] ?>">
                                        <span class="rangeSliderPercentage"
                                        <?php if ($wizardData['percentage'] > 52): ?>
                                            style="color: white;"
                                        <?php endif; ?>
                                        ><?php echo $wizardData['percentage'] ?>%</span>
                                        <span class="rangeSliderBg" style="width: <?php echo $wizardData['percentage'] ?>%;"></span>
                                    </div>

                                    <svg class="mmp_wizard__generating_svg">
                                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#refresh' ?>"></use>
                                    </svg>

                                    <svg class="mmp_wizard__generating_done_svg">
                                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#check-inv' ?>"></use>
                                    </svg>
                                </div>

                                <input type="hidden" name="token" id="token" value="<?php echo $wizardData['token'] ?>"/>
                                <input type="hidden" name="feed" id="feed" value="<?php echo $wizardData['cronAction'] ?>"/>
                                <input type="hidden" name="feedName" id="feedName" value="<?php echo $wizardData['feed'] ?>"/>
                                <input type="hidden" name="action" id="action"
                                       value="<?php echo $wizardData['ajaxGenerateAction'] ?>"/>
                            </form>
                        </div>

                    </div>

                    <?php if ($wizardForced): ?>
                        <div class="mmp_wizard__buttons mmp_justify_end">
                            <a href="javascript:void(0);" class="mmp_btn__blue"
                               data-3-generate="start"
                               data-mmp-wizard-go="3"
                               data-mmp-wizard-do-before="mmpStartWizardGenerating"
                               data-go-to-link="<?php echo $wizardData['feedListLink'] ?>"><?php echo __('Start feed generation', 'mergado-marketing-pack') ?></a>
                            <a href="javascript:void(0);" class="mmp_btn__blue" data-3-generate="skip"
                               style="display: none;" data-mmp-wizard-go="3"
                               data-mmp-wizard-do-before="mmpSkipWizard"
                               data-go-to-link="<?php echo $wizardData['feedListLink'] ?>"><?php echo __('Skip to list of feeds', 'mergado-marketing-pack') ?></a>
                            <a href="javascript:void(0);" data-go-to-link="<?php echo $wizardData['feedListLink'] ?>" class="mmp_btn__blue" style="display: none;"
                               data-3-generate="done"
                               data-mmp-wizard-go="3"
                               data-mmp-wizard-do-before="mmpGoToLink"
                            >
                                <?php echo __('Continue to list of feeds', 'mergado-marketing-pack') ?></a>
                        </div>
                    <?php else: ?>
                        <div class="mmp_wizard__buttons mmp_justify_end">
<!--                            <a href="javascript:void(0);" class="mmp_btn__white" data-mmp-wizard-go="1"-->
<!--                               data-mmp-wizard-do-before="mmpStopProgress">--><?//= __('Back', 'mergado-marketing-pack') ?><!--</a>-->
                            <a href="javascript:void(0);" class="mmp_btn__blue"
                               data-mmp-wizard-go="4a"
                               data-mmp-wizard-do-before="mmpSkipWizard"
                               data-3-generate="skip"><?php echo __('Skip to cron settings', 'mergado-marketing-pack') ?></a>
                            <a href="javascript:void(0);" class="mmp_btn__blue" style="display: none;"
                               data-mmp-wizard-do-before="setWizardCompleted"
                               data-mmp-wizard-go="4a"
                               data-3-generate="done">
                                <?php echo __('Continue to cron settings', 'mergado-marketing-pack') ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
