<?php

use Mergado\Helper\BannerHelper;

include_once( 'partials/template-mergado-marketing-pack-header.php' ); ?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <div class="card full">
                <h2><?php echo __('Licence', 'mergado-marketing-pack'); ?></h2>
                <p>
                    <?php echo __('<strong>Using the module Mergado pack is at your own risk.</strong> The creator of module, the company Mergado technologies, LLC, is not liable for any losses or damages in any form. Installing the module into your store, you agree to these terms.', 'mergado-marketing-pack'); ?>
                </p>

                <p>
                    <?php echo __('The module source code cannot be changed and modified otherwise than the user settings in the administration.', 'mergado-marketing-pack'); ?>
                </p>

                <p>
                    <?php echo __('Using the module Mergado pack within Wordpress & Woocommerce is free. Supported versions of Wordpress are starting 4.5.0 above, Woocommerce 3.1 above and PHP version 7.2 and above.', 'mergado-marketing-pack'); ?>
                </p>
            </div>
        </div>
        <div class="col-side col-side-extra">
            <?php echo BannerHelper::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?php echo BannerHelper::getWide() ?>
    </div>
</div>
