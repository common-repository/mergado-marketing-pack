<?php

use Mergado\Helper\BannerHelper;

include_once( 'partials/template-mergado-marketing-pack-header.php' ); ?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <div class="card full">
                <h1 class="mmp_h1"><?php echo __('Contact support', 'mergado-marketing-pack'); ?></h1>

                <div class="mmp_support">
                    <div class="mmp_support__form">
                        <?php include_once wp_normalize_path(__MERGADO_TEMPLATE_DIR__ . 'support/form.php')?>
                    </div>
                    <div class="mmp_support__links">
                        <?php include_once wp_normalize_path(__MERGADO_TEMPLATE_DIR__ . 'support/links.php')?>
                    </div>
                </div>
            </div>
            <div>
                <?php include_once wp_normalize_path(__MERGADO_TEMPLATE_DIR__ . 'support/reset_token_form.php')?>
            </div>

            <div class="card full">
                <?php include_once wp_normalize_path(__MERGADO_TEMPLATE_DIR__ . 'support/logs.php')?>
            </div>
        </div>
        <div class="col-side col-side-extra">
            <?php echo BannerHelper::getSidebar()?>
        </div>
    </div>
    <div class="merwide">
        <?php echo BannerHelper::getWide() ?>
    </div>
</div>
