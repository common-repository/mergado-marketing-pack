<?php
//    feedBoxData should be set in parent template
//    Functions like:
//    ProductFeed::getDataForTemplates();
//    StockFeed::getDataForTemplates();
//    CategoryFeed::getDataForTemplates();
?>

<?php

use Mergado\Service\AlertService;

if ( count( $feedBoxData['feedErrors'] ) === 0 ) {
    // No errors during generation
    if ($feedBoxData['wizardCompleted']) {
        if ( $feedBoxData['feedStatus'] === 'warning' && $feedBoxData['lastUpdate'] === false ) {
            // Not created yet
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];

            if ($feedBoxData['feedName'] === 'stock') {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulationWaitingStock.php';
            } else {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulationWaiting.php';
            }
        } else if ($feedBoxData['feedExist'] && $feedBoxData['feedStatus'] === 'success') {
            // Feed created already
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];

            if ($feedBoxData['feedName'] === 'stock') {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulationStock.php';
            } else {
                include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/congratulation.php';
            }
        }
    }
} else {
    if (in_array(AlertService::ALERT_NAMES['ERROR_DURING_GENERATION'], $feedBoxData['feedErrors'])) {
        if ($feedBoxData['feedStatus'] === 'danger') {
            // Error thrown during generation
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];
            include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedFailedBeforeFirstGeneration.php';
        } else {
            // Error thrown during generation
            $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];
            include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedFailed.php';
        }
    }

    if (in_array(AlertService::ALERT_NAMES['NO_FEED_UPDATE'], $feedBoxData['feedErrors'])) {
        // Error thrown during generation
        $alertData = [ 'alertSection' => $feedBoxData['feedSection'], 'feedName' => $feedBoxData['feedName'] ];
        include __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'alerts/feedNotUpdated.php';
    }
}

if(in_array(AlertService::ALERT_NAMES['ERROR_DURING_GENERATION'], $feedBoxData['feedErrors'])) {
    $feedStatusClass = 'mmp_feedBox__feedStatus--danger';
} else {
    $feedStatusClass = 'mmp_feedBox__feedStatus--' . $feedBoxData['feedStatus'];
}
?>

<?php if ( $feedBoxData['feedStatus'] === 'danger' ): ?>
    <div class="mmp_feedBox">
        <div class="mmp_feedBox__top">
            <div class="mmp_feedBox__status">
                <div class="mmp_feedBox__feedStatus <?php echo $feedStatusClass ?>"></div>
                <p class="mmp_feedBox__date"><?php echo __( 'Waiting for first generation', 'mergado-marketing-pack' ) ?></p>
            </div>
            <div class="mmp_feedBox__actions">
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__createXmlFeed"
                   href="<?php echo $feedBoxData['wizardUrl'] ?>">
					<?php echo __( 'Create xml feed', 'mergado-marketing-pack' ) ?>
                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg' ) . 'mmp_icons.svg#plus' ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="mmp_feedBox">
        <div class="mmp_feedBox__top">
            <div class="mmp_feedBox__status">
				<?php if ( $feedBoxData['feedStatus'] === 'success' ): ?>
                    <div class="mmp_feedBox__feedStatus <?php echo $feedStatusClass ?>"></div>
                    <p class="mmp_feedBox__date"><?php echo __( 'Last update: ', 'mergado-marketing-pack' ) ?> <?php echo $feedBoxData['lastUpdate'] ?></p>
				<?php elseif ( $feedBoxData['feedStatus'] === 'warning' ): ?>
                    <div class="mmp_feedBox__feedStatus <?php echo $feedStatusClass ?>"></div>
                    <p class="mmp_feedBox__date"><?php echo $feedBoxData['percentageStep'] ?> %
                        - <?php echo __( 'Waiting for next cron start', 'mergado-marketing-pack' ) ?></p>
				<?php endif; ?>
            </div>
            <div class="mmp_feedBox__actions">
				<?php if ( $feedBoxData['feedStatus'] === 'warning' || in_array(AlertService::ALERT_NAMES['ERROR_DURING_GENERATION'], $feedBoxData['feedErrors'])): ?>
                    <a class="mmp_feedBox__button mmp_feedBox__finishManually""
                    href="<?php echo $feedBoxData['generateUrl'] ?>"
                    data-tippy-content="<?php echo __( 'Manually finish feed creating.', 'mergado-marketing-pack' ) ?>"
                    >
                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#turn-on' ?>"></use>
                    </svg>
					<?php echo __( 'Finish manually', 'mergado-marketing-pack' ) ?>
                    </a>
				<?php elseif ( $feedBoxData['feedStatus'] === 'success' ): ?>
                    <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__openXmlFeed"
                       data-tippy-content="<?php echo __( 'Open XML feed in new window.', 'mergado-marketing-pack' ) ?>"
                       href="<?php echo $feedBoxData['feedUrl'] ?>" target="_blank">
                        <svg class="mmp_icon">
                            <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#open' ?>"></use>
                        </svg>
                    </a>
				<?php endif; ?>

                <?php if($feedBoxData['createExportInMergadoUrl'] === false): ?>
                    <a class="mmp_feedBox__button mmp_feedBox__copyUrl" href="javascript:void(0);" data-copy-stash='<?php echo $feedBoxData['feedUrl'] ?>'
                            data-tippy-content="<?php echo __( 'Copy feed URL address to clipboard. <br><br> Activate the Availability feed in Heureka administration in the Settings > Availability XML file page.', 'mergado-marketing-pack' ) ?>"
                            >
                        <svg class="mmp_icon">
                            <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#copy' ?>"></use>
                        </svg>
                        <?php echo __( 'Copy feed URL', 'mergado-marketing-pack' ) ?>
                    </a>
                <?php else: ?>
                    <a class="mmp_feedBox__export
                        <?php if ( $feedBoxData['feedStatus'] !== 'success' ): ?>
                                disabled" href="javascript:void(0);">
                        <?php else: ?>
                            " target="_blank" href="<?php echo $feedBoxData['createExportInMergadoUrl'] ?>"
                            data-tippy-content="<?php echo __( 'Click to redirect to Mergado where you can start creating exports for hundereds of different channels. <br><br> Mergado App will open in a new window.', 'mergado-marketing-pack' ) ?>"
                            >
                        <?php endif ?>
                        <p class="mmp_feedBox__button mmp_feedBox__mergadoExport"><?php echo __( 'Create export in Mergado', 'mergado-marketing-pack' ) ?></p>
                        <svg class="mmp_icon">
                            <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#service-mergado' ?>"></use>
                        </svg>
                    </a>
                <?php endif; ?>
                <a class="mmp_feedBox__toggler" href="javascript:void(0);">
                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'mmp_icons.svg' ) . 'mmp_icons.svg#chevron-down' ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
        <div class="mmp_feedBox__bottom">
            <div class="mmp_feedBox__line">
                <div class="mmp_feedBox__line--left">
                    <p class="mmp_feedBox__name"><?php echo __( 'Feed URL', 'mergado-marketing-pack' ) ?></p>
                    <input type="text" class="mmp_feedBox__url" readonly value="<?php echo $feedBoxData['feedUrl'] ?>" />
                </div>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__copy
                            <?php if ( $feedBoxData['feedStatus'] !== 'success' ): ?>
                               disabled"
				<?php else: ?>
                    " data-copy-stash='<?php echo $feedBoxData['feedUrl'] ?>'
                    data-tippy-content="<?php echo __( 'Copy feed URL address to clipboard.', 'mergado-marketing-pack' ) ?>"
				<?php endif; ?>
                href="javascript:void(0);">

                <svg class="mmp_icon">
                    <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#copy' ?>"></use>
                </svg>
                </a>
            </div>
            <div class="mmp_feedBox__line">
                <div class="mmp_feedBox__line--left">
                    <p class="mmp_feedBox__name"><?php echo __( 'Cron URL', 'mergado-marketing-pack' ) ?></p>
                    <input type="text" class="mmp_feedBox__url" readonly value="<?php echo $feedBoxData['cronGenerateUrl'] ?>">
                </div>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__copy
                    " data-copy-stash='<?php echo $feedBoxData['cronGenerateUrl'] ?>'
                    data-tippy-content="<?php echo __( 'Copy cron URL address to clipboard.', 'mergado-marketing-pack' ) ?>"
                href="javascript:void(0);">

                <svg class="mmp_icon">
                    <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#copy' ?>"></use>
                </svg>
                </a>
            </div>
            <div class="mmp_feedBox__actionsBottom">
                <a class="mmp_feedBox__button mmp_feedBox__cronSetup"
                   href="<?php echo $feedBoxData['cronSetUpUrl'] ?>"
                   data-tippy-content="<?php echo __( 'Schedule when and how often is your feed going to be updated.', 'mergado-marketing-pack' ) ?>"
                >
                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#in-progress' ?>"></use>
                    </svg>
					<?php echo __( 'Cron set up', 'mergado-marketing-pack' ) ?>
                </a>
                <a class="mmp_feedBox__button mmp_feedBox__generate"
                   href="<?php echo $feedBoxData['generateUrl'] ?>"
                   data-tippy-content="<?php echo __( 'Manually start feed creating.', 'mergado-marketing-pack' ) ?>"
                >
                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#turn-on' ?>"></use>
                    </svg>
					<?php echo __( 'Generate manually', 'mergado-marketing-pack' ) ?></a>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__download
                            <?php if ( $feedBoxData['feedStatus'] !== 'success' ): ?>
                               disabled" href="javascript:void(0);">
					<?php else: ?>
                        " href="<?php echo $feedBoxData['downloadUrl'] ?>"
                        data-tippy-content="<?php echo __( 'Download the feed to your computer.', 'mergado-marketing-pack' ) ?>"
                        >
					<?php endif; ?>

                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#download' ?>"></use>
                    </svg>
                </a>
                <a class="mmp_feedBox__button mmp_feedBox__button--square mmp_feedBox__button--danger mmp_feedBox__delete
                    <?php if ( $feedBoxData['feedStatus'] === 'danger'): ?>
                       disabled" href="javascript:void(0)">
					<?php else: ?>
                        " href="<?php echo $feedBoxData['deleteUrl'] ?>"
                        data-tippy-content="<?php echo __( 'Deletes the product feed and all links.', 'mergado-marketing-pack' ) ?>"
                        >
					<?php endif ?>
                    <svg class="mmp_icon">
                        <use xlink:href="<?php echo plugin_dir_url( __MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg' ) . 'icons.svg#delete' ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
