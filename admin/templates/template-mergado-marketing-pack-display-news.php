<?php

use Mergado\Helper\BannerHelper;
use Mergado\Service\NewsService;

$news = NewsService::getNewsWithFormatedDate(\Mergado\Helper\LanguageHelper::getLocale(), 15);

if (isset($_GET) && $_GET) {
    NewsService::setArticlesShown(null, true);
}

include_once( 'partials/template-mergado-marketing-pack-header.php' ); ?>

<div class="wrap">
    <div class="card full">
        <h2><?php echo __('Mergado news', 'mergado-marketing-pack') ?></h2>
    </div>
    <div class="rowmer">
        <div class="col-content">
            <?php
            foreach ($news as $item) { ?>
                <div class="card full <?php echo $item->category ?>">
                    <div class="card-header">
                        <h3><?php echo $item->title ?></h3>
                        <p><?php echo $item->pubDate ?></p>
                    </div>
                    <div class="mergado-pb-10"><?php echo $item->description ?></div>
                    <?php if($item->link && $item->link !== ''): ?>
                        <div class="mergado_card__footer">
                            <a class="mergado_card__commonButton" href="<?php echo $item->link; ?>" target="_blank"><?php echo __('Continue reading...', 'mergado-marketing-pack') ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="col-side col-side-extra">
            <?php echo BannerHelper::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?php echo BannerHelper::getWide() ?>
    </div>
</div>
