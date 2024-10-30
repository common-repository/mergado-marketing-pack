<?php if (@$wizardType !== $wizardData['feed'] || $wizardStep === '1' || ($wizardStep === false && !$isAlreadyFinished)): ?>
    <div class="mmp_wizard active" data-mmp-wizard-step="1">
<?php else: ?>
    <div class="mmp_wizard" data-mmp-wizard-step="1">
<?php endif ?>
    <div class="card full">
        <h1 class="mmp_wizard__heading"><?php echo __('Start creating your feeds with Mergado Pack', 'mergado-marketing-pack'); ?></h1>

        <div class="mmp_wizard__content">
            <a href="javascript:void(0);" class="mmp_btn__blue"
               data-mmp-wizard-go="3"
               data-mmp-wizard-do-before="setSelectedWizardAndGenerate"
            ><span><?php echo __('Run the setup wizard', 'mergado-marketing-pack'); ?>
                </span>
                <svg fill="white" class="mmp_wizard__plusIcon">
                    <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg') . 'mmp_icons.svg#plus' ?>"></use>
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
    .mmp_wizard[data-mmp-wizard-step="1"] .mmp_wizard__content {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 150px;
    }

    .mmp_wizard__plusIcon {
        height: 14px;
        width: 15px;
        margin-left: 8px;
    }
</style>
