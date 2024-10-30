<?php

use Mergado\Helper\LanguageHelper;
use Mergado\Service\NewsBannerService;
use Mergado\Service\NewsService;
use Mergado\Service\RatingBannerService;

$unreadedTop = NewsService::getNewsByStatusAndLanguageAndCategory(0, LanguageHelper::getLocale(), 'top');
$unreadedNews = NewsService::getNewsByStatusAndLanguageAndCategory(0, LanguageHelper::getLocale(), '', 2, false, 'DESC');

// Menu definition
$menuItemsLeft = [
    'feeds-product' => ['text' => 'Product feeds', 'icon' => 'product', 'file' => 'icons.svg'],
    'feeds-other' => ['text' => 'Other feeds', 'icon' => 'other_feeds', 'file' => 'icons.svg'],
    'adsys' => ['text' => 'Ad Systems', 'icon' => 'elements', 'file' => 'icons.svg'],
];

$menuItemsRight = [
    'cookies' => ['text' => 'Cookies', 'icon' => 'cookies', 'file' => 'mmp_icons.svg', 'param' => 'mmp-tab=cookies'],
    'news' => ['text' => 'News', 'icon' => 'notification', 'file' => 'icons.svg'],
    'support' => ['text' => 'Support', 'icon' => 'help', 'file' => 'icons.svg'],
    'licence' => ['text' => 'Licence', 'icon' => 'info', 'file' => 'icons.svg'],
];
?>


<div id="mmpheader">
    <div class="mmp-header-top">
        <h1>
            <a href="<?php echo sprintf( __( 'https://pack.mergado.%s/?utm_source=mp&utm_medium=logo&utm_campaign=mergado_pack', 'mergado-marketing-pack' ), LanguageHelper::getPackDomain()); ?>" target="_blank">
            <img class="header-logo-mmp" src="<?php echo plugins_url('mergado-marketing-pack/admin/img/') . 'mergado_pack_logo_white.svg' ?>" alt="<?php echo __('Mergado Pack', 'mergado-marketing-pack') ?>" />
            </a>
        </h1>
        <ul class="menu">
            <li>
                <a class="" href="<?php echo sprintf( __( 'https://mergado.%s/?utm_source=mp&utm_medium=logo&utm_campaign=mergado', 'mergado-marketing-pack' ), LanguageHelper::getMergadoDomain()); ?>" title="<?php echo __('Mergado', 'mergado-marketing-pack'); ?>" target="_blank">
                    <img class="header-logo-mergado" src="<?php echo plugins_url('mergado-marketing-pack/admin/img/') . 'mergado_logo.png' ?>" alt="<?php echo __('Mergado Pack', 'mergado-marketing-pack') ?>" />
                </a>
            </li>
            <li><a class="btn btn-header-primary" href="<?php echo __( 'https://accounts.mergado.com/register/?utm_source=mp&utm_medium=button&utm_campaign=register', 'mergado-marketing-pack' )?>" title="<?php echo __('New account', 'mergado-marketing-pack'); ?>" target="_blank"><?php echo __('New account', 'mergado-marketing-pack'); ?></a></li>
            <li><a class="btn btn-header-secondary" href="<?php echo __( 'https://www.mergado.com/mergado-smart-product-feed-manager/?utm_source=mp&utm_medium=button&utm_campaign=visit_web', 'mergado-marketing-pack' )?>" title="<?php echo __('Visit website', 'mergado-marketing-pack'); ?>" target="_blank"><?php echo __('Visit website', 'mergado-marketing-pack'); ?></a></li>
        </ul>
    </div>

    <div class="mmp-header-bot">
        <h2><?php echo __('Connect your e-commerce to MERGADO Multichannel Marketing', 'mergado-marketing-pack') ?></h2>
        <div class="mmp-nav-links">
            <ul class="menu-nav-links--left">
                <?php
                    foreach($menuItemsLeft as $key => $item):
                        if(('mergado-' . $key) == $_GET['page']): ?>
                            <li><a href="<?php echo admin_url('admin.php?page=mergado-' . $key . (isset($item['param']) ? '&' . $item['param'] : '')) ?>" class="active">
                                    <?php
                                        if($item['icon'] === 'other_feeds'):
                                    ?>
                                            <svg class="iconsMenu" viewBox="0 0 69 57">
                                                <path d="M28.676,17.164l-0.009,-3.662c-0.002,-0.894 0.915,-1.498 1.74,-1.147l16.604,7.075c0.458,0.195 0.757,0.644 0.758,1.142l0.043,18.18c0.002,0.893 -0.915,1.498 -1.74,1.146l-3.355,-1.429l0,-6.113c-0.158,-7.202 0.448,-8.605 -2.247,-9.827c-12.074,-5.478 -11.794,-5.365 -11.794,-5.365Z"/>
                                                <path d="M67.373,12.165c0.658,-0 1.25,0.527 1.25,1.244l0.044,18.176c0,0.497 -0.295,0.948 -0.753,1.145l-16.57,7.154c-0.163,0.07 -0.33,0.103 -0.494,0.104c-0.658,-0 -1.25,-0.528 -1.25,-1.244l-0.044,-18.18c-0.004,-0.497 0.292,-0.947 0.75,-1.145l16.57,-7.153c0.163,-0.07 0.33,-0.104 0.494,-0.105l0.003,0.004Z"/>
                                                <path d="M48.541,0c0.173,0 0.346,0.035 0.507,0.106l17.469,7.674c0.99,0.437 0.994,1.837 0.006,2.277l-17.433,7.758c-0.16,0.072 -0.334,0.108 -0.506,0.11c-0.173,-0 -0.346,-0.036 -0.507,-0.107l-17.47,-7.676c-0.99,-0.435 -0.993,-1.836 -0.005,-2.276l17.433,-7.758c0.16,-0.072 0.334,-0.108 0.506,-0.108Z"/>
                                                <path d="M0.043,48.637l-0.043,-18.18c-0.002,-0.894 0.915,-1.498 1.74,-1.147l16.604,7.075c0.458,0.195 0.757,0.644 0.758,1.142l0.043,18.18c0.002,0.893 -0.915,1.498 -1.74,1.146l-16.605,-7.074c-0.458,-0.196 -0.756,-0.644 -0.757,-1.142Zm38.663,-19.517c0.658,0 1.25,0.527 1.25,1.244l0.044,18.176c0,0.497 -0.295,0.948 -0.753,1.145l-16.57,7.154c-0.163,0.07 -0.33,0.103 -0.494,0.104c-0.658,0 -1.25,-0.528 -1.25,-1.244l-0.044,-18.18c-0.004,-0.497 0.292,-0.947 0.75,-1.145l16.57,-7.153c0.163,-0.07 0.33,-0.104 0.494,-0.105l0.003,0.004Zm-18.832,-12.165c0.173,0 0.346,0.035 0.507,0.106l17.469,7.674c0.99,0.437 0.994,1.837 0.006,2.277l-17.433,7.758c-0.16,0.072 -0.334,0.108 -0.506,0.11c-0.173,0 -0.346,-0.036 -0.507,-0.107l-17.47,-7.676c-0.99,-0.435 -0.993,-1.836 -0.005,-2.276l17.433,-7.758c0.16,-0.072 0.334,-0.108 0.506,-0.108Z" style="fill-rule:nonzero;"/>
                                            </svg>
                                    <?php
                                        else:
                                    ?>
                                            <svg class="iconsMenu">
                                                <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . $item['file'] . '#' . $item['icon'] ?>"></use>
                                            </svg>
                                    <?php
                                        endif;
                                    ?>

                                    <?php echo __( $item["text"], 'mergado-marketing-pack') ?>
                                </a></li>
                        <?php else: ?>
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=mergado-' . $key . (isset($item['param']) ? '&' . $item['param'] : '')) ?>">
                                    <?php
                                    if($item['icon'] === 'other_feeds'):
                                        ?>
                                        <svg class="iconsMenu" viewBox="0 0 69 57">
                                            <path d="M28.676,17.164l-0.009,-3.662c-0.002,-0.894 0.915,-1.498 1.74,-1.147l16.604,7.075c0.458,0.195 0.757,0.644 0.758,1.142l0.043,18.18c0.002,0.893 -0.915,1.498 -1.74,1.146l-3.355,-1.429l0,-6.113c-0.158,-7.202 0.448,-8.605 -2.247,-9.827c-12.074,-5.478 -11.794,-5.365 -11.794,-5.365Z"/>
                                            <path d="M67.373,12.165c0.658,-0 1.25,0.527 1.25,1.244l0.044,18.176c0,0.497 -0.295,0.948 -0.753,1.145l-16.57,7.154c-0.163,0.07 -0.33,0.103 -0.494,0.104c-0.658,-0 -1.25,-0.528 -1.25,-1.244l-0.044,-18.18c-0.004,-0.497 0.292,-0.947 0.75,-1.145l16.57,-7.153c0.163,-0.07 0.33,-0.104 0.494,-0.105l0.003,0.004Z"/>
                                            <path d="M48.541,0c0.173,0 0.346,0.035 0.507,0.106l17.469,7.674c0.99,0.437 0.994,1.837 0.006,2.277l-17.433,7.758c-0.16,0.072 -0.334,0.108 -0.506,0.11c-0.173,-0 -0.346,-0.036 -0.507,-0.107l-17.47,-7.676c-0.99,-0.435 -0.993,-1.836 -0.005,-2.276l17.433,-7.758c0.16,-0.072 0.334,-0.108 0.506,-0.108Z"/>
                                            <path d="M0.043,48.637l-0.043,-18.18c-0.002,-0.894 0.915,-1.498 1.74,-1.147l16.604,7.075c0.458,0.195 0.757,0.644 0.758,1.142l0.043,18.18c0.002,0.893 -0.915,1.498 -1.74,1.146l-16.605,-7.074c-0.458,-0.196 -0.756,-0.644 -0.757,-1.142Zm38.663,-19.517c0.658,0 1.25,0.527 1.25,1.244l0.044,18.176c0,0.497 -0.295,0.948 -0.753,1.145l-16.57,7.154c-0.163,0.07 -0.33,0.103 -0.494,0.104c-0.658,0 -1.25,-0.528 -1.25,-1.244l-0.044,-18.18c-0.004,-0.497 0.292,-0.947 0.75,-1.145l16.57,-7.153c0.163,-0.07 0.33,-0.104 0.494,-0.105l0.003,0.004Zm-18.832,-12.165c0.173,0 0.346,0.035 0.507,0.106l17.469,7.674c0.99,0.437 0.994,1.837 0.006,2.277l-17.433,7.758c-0.16,0.072 -0.334,0.108 -0.506,0.11c-0.173,0 -0.346,-0.036 -0.507,-0.107l-17.47,-7.676c-0.99,-0.435 -0.993,-1.836 -0.005,-2.276l17.433,-7.758c0.16,-0.072 0.334,-0.108 0.506,-0.108Z" style="fill-rule:nonzero;"/>
                                        </svg>
                                    <?php
                                    else:
                                        ?>
                                        <svg class="iconsMenu">
                                            <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . $item['file'] . '#' . $item['icon'] ?>"></use>
                                        </svg>
                                    <?php
                                    endif;
                                    ?>
                                    <?php echo __( $item['text'], 'mergado-marketing-pack') ?>
                                </a></li>
                        <?php
                        endif;
                    endforeach
                ?>
            </ul>
            <ul class="menu-nav-links--right">
                <?php
                    foreach($menuItemsRight as $key => $item):
                        if(('mergado-' . $key) == $_GET['page']): ?>
                            <li><a href="<?php echo admin_url('admin.php?page=mergado-' . $key . (isset($item['param']) ? '&' . $item['param'] : '')) ?>" class="active">
                                    <svg class="iconsMenu">
                                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . $item['file'] . '#' . $item['icon'] ?>"></use>
                                    </svg>
                                    <?php echo __( $item["text"], 'mergado-marketing-pack') ?></a></li>
                        <?php else: ?>
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=mergado-' . $key . (isset($item['param']) ? '&' . $item['param'] : '')) ?>">
                                    <svg class="iconsMenu">
                                        <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . $item['file'] . '#' . $item['icon'] ?>"></use>
                                    </svg>
                                    <?php echo __( $item['text'], 'mergado-marketing-pack') ?></a></li>
                        <?php
                        endif;
                    endforeach
                ?>
            </ul>
        </div>
    </div>
</div>

<!-- Hide other plugins notices -->
<div class="wrap" style="display: none;">
    <h2></h2>
</div>

<?php if (!isset($isIntro) || $isIntro === false): ?>
    <?php if(RatingBannerService::shouldBeRatingVisible()): ?>
    <div class="wrap">
        <div class="mergado-updated-notice news">
            <span class="dashicons dashicons-info"></span>
            <span>
                <p><?php echo __('We have noticed that you have been using Mergado Pack for some time. We hope you love it, and we would really appreciate it if you would <a href="https://wordpress.org/support/plugin/mergado-marketing-pack/reviews/#new-post">give us a 5 stars rating ★ ★ ★ ★ ★.</a>', 'mergado-marketing-pack'); ?></p>
                <a href="#" class="button button-secondary mmp-close-cross"><?php echo __('Dismiss', 'mergado-marketing-pack'); ?></a>
            </span>
            <span data-cookie="<?php echo RatingBannerService::COOKIE_RATING ?>" data-token="<?php echo $token ?>" class="mmp-cross mmp-close-cross">🞩</span>
        </div>
    </div>
    <?php endif ?>
<?php endif ?>

<?php if (!isset($isIntro)): ?>
    <?php if(isset(get_site_transient( 'update_plugins' )->response['mergado-marketing-pack/mergado-marketing-pack.php'])): ?>
        <div class="wrap">
            <div class="mergado-updated-notice update">
                <p>
                    <span class="dashicons dashicons-info"></span>
                    <span><?php echo __('New update available', 'mergado-marketing-pack')?> -&nbsp </span><a href="<?php echo admin_url('plugins.php') ?>"><?php echo __('Update to version ', 'mergado-marketing-pack') ?> <?php echo get_site_transient( 'update_plugins' )->response['mergado-marketing-pack/mergado-marketing-pack.php']->new_version ?></a>
                </p>
            </div>
        </div>
    <?php endif ?>

    <?php if($unreadedTop && $unreadedTop != []): ?>
        <?php foreach($unreadedTop as $item): ?>
            <div class="wrap">
                <div class="mergado-updated-notice update">
                    <p>
                        <span class="dashicons dashicons-info"></span>
                        <a href="<?php echo admin_url('admin.php?page=mergado-news') ?>" class="mergado-link"><span><?php echo $item->title ?></span></a>
                    </p>
                    <span data-todo="mmp-set-readed" data-id="<?php echo $item->id ?>" data-token="<?php echo $token ?>" class="mmp-cross mmp-readed-cross">🞩</span>
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>

    <?php if(NewsBannerService::shouldBeVisible()): ?>
        <?php if($unreadedNews && $unreadedNews != []): ?>
            <div class="wrap">
                <div class="mergado-updated-notice news">

                    <div class="mmp-news__holder">
                        <?php foreach($unreadedNews as $item): ?>
                        <div class="mmp-news__item">
                                <a href="<?php echo admin_url('admin.php?page=mergado-news') ?>" class="mergado-link">
                                    <p class="mmp-news__title"><?php echo $item->title ?></p>
                                </a>
                                <p><span class="mmp-badge mmp-badge--<?php echo $item->category ?>"><?php echo $item->category ?></span> <span class="mmp-news__date"><?php echo NewsService::getFormattedDate($item->pubDate) ?></span></p>

                            <?php if($item->link && $item->link !== ''): ?>
                                <div class="mmp-news__linkContainer">
                                    <a class="mmp-news__link--common" href="<?php echo $item->link ?>" target="_blank">
                                        <?php echo __( 'Continue reading...', 'mergado-marketing-pack' ) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach ?>
                    </div>

                    <span data-cookie="mmp-cookie-news" data-token="<?php echo $token ?>" class="mmp-cross mmp-close-cross">🞩</span>
                </div>
            </div>
        <?php endif ?>
    <?php endif ?>
<?php endif ?>

<?php if (isset($isIntro) && $isIntro === true): ?>
    <div id="mmpabout">
        <h3><?php echo __('CONNECT YOUR ONLINE STORE TO MERGADO MULTICHANNEL MARKETING', 'mergado-marketing-pack'); ?></h3>

        <img src="<?php echo plugins_url('mergado-marketing-pack/admin/img/') . 'info.svg' ?>" alt="" />

        <div class="mmpabout__content">
            <div class="mmpabout__left">
                <p><?php echo __( 'Try MERGADO Editor', 'mergado-marketing-pack' )?></p>
                <p><strong><?php echo __( '30 days FREE trial', 'mergado-marketing-pack' )?></strong></p>
                <a class="mmpabout__more" target="_blank" href="https://www.mergado.com/mergado-smart-product-feed-manager"><?php echo __( 'Learn more', 'mergado-marketing-pack' )?></a>

                <div class="mmpabout__btnHolder">
                    <a class="mmpabout__btn" href="<?php echo admin_url('admin.php?page=mergado-feeds-product') ?>">
                        <svg class="iconsMenu">
                            <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#product' ?>"></use>
                        </svg>

                        <?php echo __( 'Start creating feeds', 'mergado-marketing-pack' )?></a>
                </div>
            </div>
            <div class="mmpabout__right">
                <p><?php echo __( 'Implement Advertising services', 'mergado-marketing-pack' )?></p>
                <p><?php echo __( 'Into your website', 'mergado-marketing-pack' )?></p>
                <div class="mmpabout__spacer"></div>

                <div class="mmpabout__btnHolder">
                    <a class="mmpabout__btn" href="<?php echo admin_url('admin.php?page=mergado-adsys') ?>">
                        <svg class="iconsMenu">
                            <use xlink:href="<?php echo plugin_dir_url(__MERGADO_ADMIN_IMAGES_DIR__ . 'icons.svg') . 'icons.svg#elements' ?>"></use>
                        </svg>

                        <?php echo __( 'Implement ad systems', 'mergado-marketing-pack' )?></a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(($newsCount = count(NewsService::getNewsByStatusAndLanguageAndCategory(0, LanguageHelper::getLocale()))) > 0) {?>
        <div class="mergado_new_news hidden" data-news-count="<?php echo $newsCount; ?>" data-news="<?php echo __('NEWS', 'mergado-marketing-pack') ?>"></div>
<?php } ?>


<!-- MMP POPUP -->

<div class="mmp-popup" data-500="<?php echo __('Error occurred during feed import. Please contact our support.', 'mergado-marketing-pack') ?>">
    <div class="mmp-popup__background">
        <div class="mmp-popup__box">
            <div class="mmp-popup__box_top">
                <h3 class="mmp-popup__title"><?php echo __('Import prices', 'mergado-marketing-pack') ?></h3>
            </div>
            <div class="mmp-popup__box_body">
                <div class="mmp-popup__content">
                    <div class="mmp-popup__loader">
                        <div class="sk-chase">
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                        </div>
                        <div><?php echo __('Importing prices. Please wait...', 'mergado-marketing-pack') ?></div>
                    </div>
                    <p class="mmp-popup__output"></p>
                </div>
            </div>
            <div class="mmp-popup__box_foot">
                <a href="#" class="mmp-popup__button button button-primary button-large"><?php echo __('Close') ?></a>
            </div>
        </div>
    </div>
</div>
