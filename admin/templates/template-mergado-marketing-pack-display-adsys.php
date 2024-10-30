<?php

use Mergado\Helper\BannerHelper;
use Mergado\Manager\DatabaseManager;
use Mergado\Service\CookieService;
use Mergado\Service\External\Argep\ArgepService;
use Mergado\Service\External\ArukeresoFamily\Arukereso\ArukeresoService;
use Mergado\Service\External\ArukeresoFamily\Compari\CompariService;
use Mergado\Service\External\ArukeresoFamily\Pazaruvaj\PazaruvajService;
use Mergado\Service\External\Biano\BianoService;
use Mergado\Service\External\Biano\BianoStarService;
use Mergado\Service\External\Etarget\EtargetService;
use Mergado\Service\External\Facebook\FacebookService;
use Mergado\Service\External\Glami\GlamiPixelService;
use Mergado\Service\External\Glami\GlamiTopService;
use Mergado\Service\External\Google\GoogleAds\GoogleAdsService;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\Ga4Service;
use Mergado\Service\External\Google\GoogleAnalytics\GoogleAnalyticsRefundService;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalService;
use Mergado\Service\External\Google\GoogleReviews\GoogleReviewsService;
use Mergado\Service\External\Google\GoogleTagManager\GoogleTagManagerService;
use Mergado\Service\External\Heureka\HeurekaCzService;
use Mergado\Service\External\Heureka\HeurekaSkService;
use Mergado\Service\External\Kelkoo\KelkooService;
use Mergado\Service\External\NajNakup\NajNakupService;
use Mergado\Service\External\Pricemania\PricemaniaService;
use Mergado\Service\External\Sklik\SklikService;
use Mergado\Service\External\Zbozi\ZboziService;

include_once( 'partials/template-mergado-marketing-pack-header.php' );

// Todo move us to our files and import here

if (isset($_POST["submit-save"])) {

    /**
     * Glami PiXel settings
     */
    GlamiPixelService::saveFields($_POST);

    /**
     * Biano settings
     */
    BianoService::saveFields($_POST);

    /**
     * Biano Star settings
     */
    BianoStarService::saveFields($_POST);

    /**
     * Glami TOP settings
     */
    GlamiTopService::saveFields($_POST);

    /**
     * Facebook settings
     */
    FacebookService::saveFields($_POST);

    /**
     * Sklik settings
     */
    SklikService::saveFields($_POST);

    /**
     * Adwords settings
     */

    GoogleAdsService::saveFields($_POST);

    /**
     * Google analytics (gtag.js) settings
     */
    GaUniversalService::saveFields($_POST);


    /**
     * GoogleAnalytics 4
     */
    Ga4Service::saveFields($_POST);


	/**
	 * GaRefund settings
	 */
	GoogleAnalyticsRefundService::saveFields($_POST);

    /**
     * Google reviews settings
     */
    GoogleReviewsService::saveFields($_POST);

    /**
     * Árukereső
     */
    ArukeresoService::saveFields($_POST);

    /**
     * Compari
     */
    CompariService::saveFields($_POST);


    /**
     * Compari
     */
    PazaruvajService::saveFields($_POST);

    /**
     * Google analytics (Google Tag Manager) settings
     */
    GoogleTagManagerService::saveFields($_POST);

    /**
     * ETARGET settings
     */
    EtargetService::saveFields($_POST);

    /**
     * NajNakup settings
     */
    NajNakupService::saveFields($_POST);

    /**
     * Pricemania settings
     */
    PricemaniaService::saveFields($_POST);

    /**
     * Zbozi settings
     */
    ZboziService::saveFields($_POST);


    /**
     * Kelkoo settings
     */
    KelkooService::saveFields($_POST);

    /**
     * Argep service
     */
    ArgepService::saveFields($_POST);

    /**
     * Heureka settings
     */
    HeurekaCzService::saveFields($_POST);
    HeurekaSkService::saveFields($_POST);

    $OptOutTexts = [];

    foreach(get_available_languages() as $lang) {
       $OptOutTexts[] = 'heureka-verify-opt-out-text-' . $lang;
    }

    $OptOutTexts[] = 'heureka-verify-opt-out-text-en_US';

    DatabaseManager::saveOptions($_POST, [], $OptOutTexts);

    CookieService::saveFields($_POST);
}

$tabsSettings = [
    'tabs' => [
        'cookies' => ['title' => 'Cookies', 'icon' => 'mmp_icons.svg#cookies'],
        'google' => ['title' => 'Google', 'active' => true],
        'facebook' => ['title' => 'Facebook'],
        'heureka' => ['title' => 'Heureka'],
        'glami' => ['title' => 'GLAMI'],
        'seznam' => ['title' => 'Seznam'],
        'etarget' => ['title' => 'Etarget'],
        'najnakup' => ['title' => 'Najnakup.sk'],
        'pricemania' => ['title' => 'Pricemania'],
        'kelkoo' => ['title' => 'Kelkoo'],
        'biano' => ['title' => 'Biano'],
        'arukereso' => ['title' => 'Árukereső'],
        'compari' => ['title' => 'Compari'],
        'pazaruvaj' => ['title' => 'Pazaruvaj'],
        'argep' => ['title' => 'Árgép']
    ],
    'tabContentPath' => wp_normalize_path( __DIR__ . '/partials/tabs-adsys/adsys-' )
];
?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <form method="post" id="glami-form" action="" class="mmp-tabs--horizontal">
                <?php include( __MERGADO_TEMPLATE_COMPONENTS_DIR__ . 'tabs/tabs.php' ); ?>
            </form>
        </div>
        <div class="col-side col-side-extra">
            <?php echo BannerHelper::getSidebar() ?>
        </div>
    </div>
    <div class="merwide">
        <?php echo BannerHelper::getWide() ?>
    </div>
</div>
