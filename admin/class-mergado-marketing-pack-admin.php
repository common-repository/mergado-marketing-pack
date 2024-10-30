<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/admin
 */

use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Helper\UrlHelper;
use Mergado\Manager\TokenManager;
use Mergado\Request\Request;
use Mergado\Service\AlertService;
use Mergado\Service\Cron\CronService;
use Mergado\Service\Ean\EanService;
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
use Mergado\Service\Menu\AdminMenuService;
use Mergado\Service\ProductPriceImportService;
use Mergado\Service\RssService;
use Mergado\Utils\TemplateLoader;
use Mergado\Utils\TypeConverter;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/admin
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */

require_once(ABSPATH . 'wp-admin/includes/plugin.php'); // Imported because of is_plugin_active usage

if (!is_plugin_active('woocommerce/woocommerce.php')) {
    deactivate_plugins(plugin_basename(__MERGADO_BASE_FILE__));
    die(WOOCOMMERCE_DEPENCENCY_MESSAGE);
} else {
    if (!defined('WC_ABSPATH')) {
        define('WC_ABSPATH', __DIR__ . '/../../woocommerce/');
    }
    include_once(WC_ABSPATH . '/includes/export/class-wc-product-csv-exporter.php');
    if (!class_exists('WC_CSV_Batch_Exporter', false)) {
        include_once(WC_ABSPATH . '/includes/export/abstract-wc-csv-batch-exporter.php');
    }
    if (!class_exists('WC_CSV_Exporter', false)) {
        include_once(WC_ABSPATH . '/includes/export/abstract-wc-csv-exporter.php');
    }
}

class Mergado_Marketing_Pack_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        self::init_hooks();
    }


    public static function init_hooks()
    {
        // Init menu
        if (is_admin()) {
            add_action('admin_menu', [AdminMenuService::getInstance(), 'init']);

            add_action('init', function () {
                RssService::getInstance()->update();
            });
        }
    }

    /**
     * This method is called only in register menu calls,
     * which are protected to be called only when manage_option permission is available for user.
     */
    public static function display()
    {
        global $wp;
        $token = TokenManager::getToken();

        $feed = Request::getVariable('feed');
        $page = Request::getPage();

        $requestToken = Request::getToken();
        $action = Request::getAction();

        // Actions
        if ($action && TokenManager::tokenMatches($requestToken)) {
            switch ($action) {
                case 'downloadFeed':
                    switch ($feed) {
                        case 'product':
                            $productFeed = new ProductFeed();
                            $productFeed->downloadFeed(UrlHelper::getAdminRoute($page));
                            break;
                        case 'category':
                            $categoryFeed = new CategoryFeed();
                            $categoryFeed->downloadFeed(UrlHelper::getAdminRoute($page));
                            break;
                        case 'stock':
                            $stockFeed = new StockFeed();
                            $stockFeed->downloadFeed(UrlHelper::getAdminRoute($page));
                            break;
                        case 'customer';
                            $customerFeed = new CustomerFeed();
                            $customerFeed->downloadFeed(UrlHelper::getAdminRoute($page));
                            break;
                    }
                    break;
                case 'deleteFeed':
                    switch ($feed) {
                        case 'product':
                            $xmlClass = new ProductFeed();
                            $file = $xmlClass->getFeedPath();
                            $redirectUrl = UrlHelper::getAdminRoute('mergado-feeds-product',
                                [
                                    'mmp-tab' => 'product'
                                ]);
                            break;
                        case 'category':
                            $xmlClass = new CategoryFeed();
                            $file = $xmlClass->getFeedPath();
                            $redirectUrl = UrlHelper::getAdminRoute('mergado-feeds-other',
                                [
                                    'mmp-tab' => 'category'
                                ]);

                            break;
                        case 'customer':
                            $xmlClass = new CustomerFeed();
                            $file = $xmlClass->getFeedPath();
                            $redirectUrl = UrlHelper::getAdminRoute('mergado-feeds-other',
                                [
                                    'mmp-tab' => 'customer'
                                ]);

                            break;
                        case 'stock':
                            $xmlClass = new StockFeed();
                            $file = $xmlClass->getFeedPath();
                            $redirectUrl = UrlHelper::getAdminRoute('mergado-feeds-other',
                                [
                                    'mmp-tab' => 'stock'
                                ]);

                            break;
                    }

                    if (isset($xmlClass) && isset($file) && isset($redirectUrl)) {
                        unlink($file);
                        $xmlClass->setGenerationStep(0);
                        $xmlClass->deleteTemporaryFiles();
                        $alertService = AlertService::getInstance();
                        $alertService->setErrorInactive($feed, AlertService::ALERT_NAMES['ERROR_DURING_GENERATION']);
                        wp_redirect($redirectUrl);
                    }

                    break;
                default:
                    $params = [];

                    $tab = Request::getVariable('mmp-tab');

                    if ($tab) {
                        $params['mmp-tab'] = $tab;
                    }

                    wp_redirect(UrlHelper::getAdminRoute($page,
                        $params));
            }
        }

        // Pages
        if ($page) {
            switch ($page) {
                case 'mergado-config':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-info.php');
                    break;
                case 'mergado-cron':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-cron.php');
                    break;
                case 'mergado-feed':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-feed.php');
                    break;
                case 'mergado-licence':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-licence.php');
                    break;
                case 'mergado-support':
                    echo TemplateLoader::getTemplate(__DIR__ . '/templates/template-mergado-marketing-pack-display-support.php', [
                        'settingsData' => self::getSupportPageData()
                    ]);
                    break;
                case 'mergado-cookies':
                case 'mergado-adsys':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-adsys.php');
                    break;
                case 'mergado-news':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-news.php');
                    break;
                case 'mergado-feeds-product':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-feeds-product.php');
                    break;
                case 'mergado-feeds-other':
                    require_once(__DIR__ . '/templates/template-mergado-marketing-pack-display-feeds-other.php');
                    break;
                default:
                    exit;
                    break;
            }
        }
    }

    /*******************************************************************************************************************
     * ENQUEUE
     *******************************************************************************************************************/

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/mergado-marketing-pack-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-news', plugin_dir_url(__FILE__) . 'css/mmp-news.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-news-topbar', plugin_dir_url(__FILE__) . 'css/mmp-news-topbar.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-news-header', plugin_dir_url(__FILE__) . 'css/mmp-news-header.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-tabs', plugin_dir_url(__FILE__) . 'css/mmp-tabs.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-yesno', plugin_dir_url(__FILE__) . 'vendors/yesno/src/index.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . '-tabs', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-tabs.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-wizard', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-wizard.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . '-dropdownBox', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-dropdown.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-popper', plugin_dir_url(__FILE__) . 'vendors/tippy/popper.min.js', false, $this->version, true);
        wp_enqueue_script($this->plugin_name . '-tooltip', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-tooltip.js', false, $this->version, true);
        wp_enqueue_script($this->plugin_name . '-tippy', plugin_dir_url(__FILE__) . 'vendors/tippy/tippy-bundle.umd.min.js', false, $this->version, true);
        wp_enqueue_script($this->plugin_name . '-yesno', plugin_dir_url(__FILE__) . 'vendors/yesno/src/index.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-cron-estimate', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-cron-estimate.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-alerts', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-alerts.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-toggle-fields', plugin_dir_url(__FILE__) . 'js/mergado-marketing-pack-admin-toggle-fields.js', array('jquery'), $this->version, true);
    }

    //TODO: move me
    public function setDefaultEan()
    {
        if (is_admin() && get_option('mmp_plugin_ean_default_set', 0) == 0) {

            try {
                // if plugin option not set before
                if (get_option(EanService::EAN_PLUGIN, 'neverSelected') === 'neverSelected') {
                    EanService::getDefaultEanAfterInstalation();
                }

                update_option('mmp_plugin_ean_default_set', 1, true);
            } catch (Exception $e) {
                // Not important
            }
        }
    }

    public static function getSupportPageData()
    {
        $productFeed = new ProductFeed();
        $categoryFeed = new CategoryFeed();
        $customerFeed = new CustomerFeed();
        $stockFeed = new StockFeed();
        $productPriceImportService = ProductPriceImportService::getInstance();
        $googleAdsService = GoogleAdsService::getInstance();
        $googleTagManagerService = GoogleTagManagerService::getInstance();
        $googleReviewsService = GoogleReviewsService::getInstance();
        $googleAnalyticsRefundService = GoogleAnalyticsRefundService::getInstance();
        $arukeresoService = ArukeresoService::getInstance();
        $compariService = CompariService::getInstance();
        $pazaruvajService = PazaruvajService::getInstance();
        $glamiPixelService = GlamiPixelService::getInstance();
        $glamiTopService = GlamiTopService::getInstance();
        $zboziService = ZboziService::getInstance();
        $facebookService = FacebookService::getInstance();
        $etargetService = EtargetService::getInstance();
        $najNakupService = NajNakupService::getInstance();
        $pricemaniaService = PricemaniaService::getInstance();
        $bianoService = BianoService::getInstance();
        $bianoStarService = BianoStarService::getInstance();
        $googleUniversalAnalyticsService = GaUniversalService::getInstance();
        $ga4Service = Ga4Service::getInstance();
        $kelkooService = KelkooService::getInstance();
        $sklikService = SklikService::getInstance();

        if (class_exists('WooCommerce')) {
            global $woocommerce;
            $woocomerceVersion = $woocommerce->version;
        } else {
            $woocomerceVersion = __('Not available', 'mergado-marketing-pack');
        }

        return [
            'base' => [
                'web_url' => [
                    'name' => __('Web URL', 'mergado-marketing-pack'),
                    'value' => get_site_url(),
                ],
                'token' => [
                    'name' => __('Token', 'mergado-marketing-pack'),
                    'value' => get_option('mmp_token'),
                ],
                'wp_version' => [
                    'name' => __('WP version', 'mergado-marketing-pack'),
                    'value' => get_bloginfo('version'),
                ],
                'wc_version' => [
                    'name' => __('WC version', 'mergado-marketing-pack'),
                    'value' => $woocomerceVersion,
                ],
                'mp_version' => [
                    'name' => __('MP version', 'mergado-marketing-pack'),
                    'value' => PLUGIN_VERSION,
                ],
                'php' => [
                    'name' => __('PHP', 'mergado-marketing-pack'),
                    'value' => phpversion(),
                ],
                'product_feed_url' => [
                    'name' => __('Product feed URL', 'mergado-marketing-pack'),
                    'value' => $productFeed->getFeedUrl(),
                ],
                'product_cron_url' => [
                    'name' => __('Product cron URL', 'mergado-marketing-pack'),
                    'value' => $productFeed->getCronUrl(),
                ],
                'product_feed_last_change' => [
                    'name' => __('Product feed last change time', 'mergado-marketing-pack'),
                    'value' => $productFeed->getLastFeedChange(),
                ],
                'product_wp_cron_active' => [
                    'name' => __('WP product cron - status', 'mergado-marketing-pack'),
                    'value' => TypeConverter::boolToActive($productFeed->isWpCronActive()),
                ],
                'product_wp_cron_schedule' => [
                    'name' => __('WP product cron - schedule', 'mergado-marketing-pack'),
                    'value' => CronService::getTaskByVariable($productFeed->getCronSchedule()),
                ],
                'category_feed_url' => [
                    'name' => __('Category feed URL', 'mergado-marketing-pack'),
                    'value' => $categoryFeed->getFeedUrl(),
                ],
                'category_cron_url' => [
                    'name' => __('Category cron URL', 'mergado-marketing-pack'),
                    'value' => $categoryFeed->getCronUrl(),
                ],
                'category_wp_cron_active' => [
                    'name' => __('WP category cron - status', 'mergado-marketing-pack'),
                    'value' => TypeConverter::boolToActive($categoryFeed->isWpCronActive()),
                ],
                'category_wp_cron_schedule' => [
                    'name' => __('WP category cron - schedule', 'mergado-marketing-pack'),
                    'value' => CronService::getTaskByVariable($categoryFeed->getCronSchedule()),
                ],
                'customer_feed_url' => [
                    'name' => __('Customer feed URL', 'mergado-marketing-pack'),
                    'value' => $customerFeed->getFeedUrl(),
                ],
                'customer_cron_url' => [
                    'name' => __('Customer cron URL', 'mergado-marketing-pack'),
                    'value' => $customerFeed->getCronUrl(),
                ],
                'customer_wp_cron_active' => [
                    'name' => __('WP customer cron - status', 'mergado-marketing-pack'),
                    'value' => TypeConverter::boolToActive($customerFeed->isWpCronActive()),
                ],
                'customer_wp_cron_schedule' => [
                    'name' => __('WP customer cron - schedule', 'mergado-marketing-pack'),
                    'value' => CronService::getTaskByVariable($customerFeed->getCronSchedule()),
                ],
                'stock_feed_url' => [
                    'name' => __('Stock feed URL', 'mergado-marketing-pack'),
                    'value' => $stockFeed->getFeedUrl(),
                ],
                'stock_cron_url' => [
                    'name' => __('Stock cron URL', 'mergado-marketing-pack'),
                    'value' => $stockFeed->getCronUrl(),
                ],
                'stock_wp_cron_active' => [
                    'name' => __('WP stock cron - status', 'mergado-marketing-pack'),
                    'value' => TypeConverter::boolToActive($stockFeed->isWpCronActive()),
                ],
                'stock_wp_cron_schedule' => [
                    'name' => __('WP stock cron - schedule', 'mergado-marketing-pack'),
                    'value' => CronService::getTaskByVariable($stockFeed->getCronSchedule()),
                ],
                'import_feed_url' => [
                    'name' => __('Import prices feed URL', 'mergado-marketing-pack'),
                    'value' => $productPriceImportService->getImportUrl(),
                ],
                'import_cron_url' => [
                    'name' => __('Import prices cron URL', 'mergado-marketing-pack'),
                    'value' => $productPriceImportService->getCronUrl(),
                ],
                'import_wp_cron_active' => [
                    'name' => __('WP import cron - status', 'mergado-marketing-pack'),
                    'value' => TypeConverter::boolToActive($productPriceImportService->isWpCronActive()),
                ],
                'import_wp_cron_schedule' => [
                    'name' => __('WP import cron - schedule', 'mergado-marketing-pack'),
                    'value' => CronService::getTaskByVariable($productPriceImportService->getCronSchedule()),
                ],
            ],
            'adsystems' => [
                'googleAds' => TypeConverter::boolToActive($googleAdsService->getConversionActive()),
                'googleAdsRemarketing' => TypeConverter::boolToActive($googleAdsService->getRemarketingActive()),
                'googleAnalytics4' => TypeConverter::boolToActive($ga4Service->getActive()),
                'googleAnalyticsEcommerce' => TypeConverter::boolToActive($ga4Service->getEcommerce()),
                'googleAnalytics' => TypeConverter::boolToActive($googleUniversalAnalyticsService->getActive()),
                'googleAnalyticsEcoomerceActive' => TypeConverter::boolToActive($googleUniversalAnalyticsService->getEcommerce()),
                'googleAnalyticsEnhancedEcoomerceActive' => TypeConverter::boolToActive($googleUniversalAnalyticsService->getEnhancedEcommerce()),
                'googleTagManager' => TypeConverter::boolToActive($googleTagManagerService->getActive()),
                'googleTagManagerEcommerce' => TypeConverter::boolToActive($googleTagManagerService->getEcommerceActive()),
                'googleTagManagerEnhancedEcommerce' => TypeConverter::boolToActive($googleTagManagerService->getEnhancedEcommerceActive()),
                'googleCustomerReviews' => TypeConverter::boolToActive($googleReviewsService->getOptInActive()),
                'googleCustomerReviewsBadge' => TypeConverter::boolToActive($googleReviewsService->getBadgeActive()),
                'facebookPixel' => TypeConverter::boolToActive($facebookService->getActive()),
                'heurekaVerify' => TypeConverter::boolToActive(get_option(HeurekaCzService::VERIFIED_ACTIVE, 0)),
                'heurekaVerifyWidget' => TypeConverter::boolToActive(get_option(HeurekaCzService::WIDGET_ACTIVE, 0)),
                'heurekaConversions' => TypeConverter::boolToActive(get_option(HeurekaCzService::CONVERSION_ACTIVE, 0)),
                'heurekaVerifySk' => TypeConverter::boolToActive(get_option(HeurekaSkService::VERIFIED_ACTIVE, 0)),
                'heurekaVerifySkWidget' => TypeConverter::boolToActive(get_option(HeurekaSkService::WIDGET_ACTIVE, 0)),
                'heurekaConversionsSk' => TypeConverter::boolToActive(get_option(HeurekaSkService::CONVERSION_ACTIVE, 0)),
                'glamiPixel' => TypeConverter::boolToActive($glamiPixelService->getActive()),
                'glamiTop' => TypeConverter::boolToActive($glamiTopService->getActive()),
                'sklik' => TypeConverter::boolToActive($sklikService->getConversionActive()),
                'sklikRetargeting' => TypeConverter::boolToActive($sklikService->getRetargetingActive()),
                'zbozi' => TypeConverter::boolToActive($zboziService->getActive()),
                'etarget' => TypeConverter::boolToActive($etargetService->getActive()),
                'najnakup' => TypeConverter::boolToActive($najNakupService->getActive()),
                'pricemania' => TypeConverter::boolToActive($pricemaniaService->getActive()),
                'kelkoo' => TypeConverter::boolToActive($kelkooService->getActive()),
                'biano' => TypeConverter::boolToActive($bianoService->getActive()),
                'bianoStar' => TypeConverter::boolToActive($bianoStarService->getActive()),
                'arukereso' => TypeConverter::boolToActive($arukeresoService->getActive()),
                'arukeresoWidget' => TypeConverter::boolToActive($arukeresoService->getWidgetActive()),
                'compari' => TypeConverter::boolToActive($compariService->getActive()),
                'compariWidget' => TypeConverter::boolToActive($compariService->getWidgetActive()),
                'pazaruvaj' => TypeConverter::boolToActive($pazaruvajService->getActive()),
                'pazaruvajWidget' => TypeConverter::boolToActive($pazaruvajService->getWidgetActive()),
            ]
        ];
    }
}
