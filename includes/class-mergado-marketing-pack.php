<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 */

use Mergado\Service\AdminBarService;
use Mergado\Service\AdminNoticeService;
use Mergado\Service\ApiService;
use Mergado\Service\CookieService;
use Mergado\Service\Cron\CronActionService;
use Mergado\Service\Cron\CronScheduleService;
use Mergado\Service\External\Argep\ArgepServiceIntegration;
use Mergado\Service\External\ArukeresoFamily\Arukereso\ArukeresoServiceIntegration;
use Mergado\Service\External\ArukeresoFamily\Compari\CompariServiceIntegration;
use Mergado\Service\External\ArukeresoFamily\Pazaruvaj\PazaruvajServiceIntegration;
use Mergado\Service\External\Biano\BianoServiceIntegration;
use Mergado\Service\External\Biano\BianoStarServiceIntegration;
use Mergado\Service\External\Etarget\EtargetServiceIntegration;
use Mergado\Service\External\Facebook\FacebookServiceIntegration;
use Mergado\Service\External\Glami\GlamiPixelServiceIntegration;
use Mergado\Service\External\Glami\GlamiTopServiceIntegration;
use Mergado\Service\External\Google\GoogleAds\GoogleAdsServiceIntegration;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\Ga4ServiceIntegration;
use Mergado\Service\External\Google\GoogleAnalytics\GoogleAnalyticsRefundService;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalService;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalServiceIntegration;
use Mergado\Service\External\Google\GoogleReviews\GoogleReviewsService;
use Mergado\Service\External\Google\GoogleTagManager\GoogleTagManagerServiceIntegration;
use Mergado\Service\External\Google\Gtag\GtagIntegrationHelper;
use Mergado\Service\External\Heureka\HeurekaServiceIntegration;
use Mergado\Service\External\Kelkoo\KelkooServiceIntegration;
use Mergado\Service\External\NajNakup\NajNakupServiceIntegration;
use Mergado\Service\External\Pricemania\PricemaniaServiceIntegration;
use Mergado\Service\External\Sklik\SklikServiceIntegration;
use Mergado\Service\External\Zbozi\ZboziServiceIntegration;
use Mergado\Service\MigrationService;
use Mergado\Service\PluginPageSettingsService;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/includes
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */
class Mergado_Marketing_Pack
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Mergado_Marketing_Pack_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    //Data that should be shown in footer after adding to cart
    protected $headerExtra = '';

    /**
     * @var GoogleTagManagerServiceIntegration
     */
    private $googleTagManagerServiceIntegration;

	/**
	 * @var EtargetServiceIntegration
	 */
    private $etargetServiceIntegration;

	/**
	 * @var KelkooServiceIntegration
	 */
    private $kelkooServiceIntegration;

	/**
	 * @var BianoServiceIntegration
	 */
    private $bianoServiceIntegration;

    /**
     * @var BianoStarServiceIntegration
     */
    private $bianoStarServiceIntegration;

    /**
     * @var CompariServiceIntegration
     */
    private $compariServiceIntegration;

    /**
     * @var ArukeresoServiceIntegration
     */
    private $arukeresoServiceIntegration;

    /**
     * @var PazaruvajServiceIntegration
     */
    private $pazaruvajServiceIntegration;

    /**
     * @var GaUniversalServiceIntegration
     */
    private $googleUniversalAnalyticsServiceIntegration;
    /**
     * @var GtagIntegrationHelper
     */
    private $gtagIntegration;
    /**
     * @var GoogleAdsServiceIntegration
     */
    private $googleAdsServiceIntegration;
    /**
     * @var Ga4ServiceIntegration
     */
    private $ga4ServiceIntegration;

    /**
     * @var ArgepServiceIntegration
     */
    private $argepServiceIntegration;

    /**
     * @var SklikServiceIntegration
     */
    private $sklikServiceIntegration;

    /**
     * @var FacebookServiceIntegration
     */
    private $facebookServiceIntegration;

    /**
     * @var GlamiPixelServiceIntegration
     */
    private $glamiPixelServiceIntegration;

    /**
     * @var GlamiTopServiceIntegration
     */
    private $glamiTopServiceIntegration;

    /**
     * @var ZboziServiceIntegration
     */
    private $zboziServiceIntegration;

    /**
     * @var HeurekaServiceIntegration
     */
    private $heurekaServiceIntegration;

    /**
     * @var NajNakupServiceIntegration
     */
    private $najNakupServiceIntegration;

    /**
     * @var PricemaniaServiceIntegration
     */
    private $pricemaniaServiceIntegration;

    /**
     * @var CookieService
     */
    private $cookieService;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('PLUGIN_VERSION')) {
            $this->version = PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'mergado-marketing-pack';


        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        MigrationService::getInstance()->migrate();

        // Services inited
        $this->glamiTopServiceIntegration = GlamiTopServiceIntegration::getInstance();

        // Integrations
        $this->etargetServiceIntegration = EtargetServiceIntegration::getInstance();
        $this->kelkooServiceIntegration = KelkooServiceIntegration::getInstance();
        $this->bianoServiceIntegration = BianoServiceIntegration::getInstance();
        $this->bianoStarServiceIntegration = BianoStarServiceIntegration::getInstance();
        $this->arukeresoServiceIntegration = ArukeresoServiceIntegration::getInstance();
        $this->compariServiceIntegration = CompariServiceIntegration::getInstance();
        $this->pazaruvajServiceIntegration = PazaruvajServiceIntegration::getInstance();
        $this->googleUniversalAnalyticsServiceIntegration = GaUniversalServiceIntegration::getInstance();
        $this->gtagIntegration = GtagIntegrationHelper::getInstance();
        $this->googleAdsServiceIntegration = GoogleAdsServiceIntegration::getInstance();
        $this->ga4ServiceIntegration = Ga4ServiceIntegration::getInstance();
        $this->argepServiceIntegration = ArgepServiceIntegration::getInstance();
        $this->sklikServiceIntegration = SklikServiceIntegration::getInstance();
        $this->facebookServiceIntegration = FacebookServiceIntegration::getInstance();
        $this->glamiPixelServiceIntegration = GlamiPixelServiceIntegration::getInstance();
        $this->zboziServiceIntegration = ZboziServiceIntegration::getInstance();
        $this->googleTagManagerServiceIntegration = GoogleTagManagerServiceIntegration::getInstance();
        $this->heurekaServiceIntegration = HeurekaServiceIntegration::getInstance();
        $this->najNakupServiceIntegration = NajNakupServiceIntegration::getInstance();
        $this->pricemaniaServiceIntegration = PricemaniaServiceIntegration::getInstance();

        $this->cookieService = CookieService::getInstance();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Mergado_Marketing_Pack_Loader. Orchestrates the hooks of the plugin.
     * - Mergado_Marketing_Pack_i18n. Defines internationalization functionality.
     * - Mergado_Marketing_Pack_Admin. Defines all hooks for the admin area.
     * - Mergado_Marketing_Pack_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(__DIR__) . 'includes/class-mergado-marketing-pack-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(__DIR__) . 'includes/class-mergado-marketing-pack-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(__DIR__) . 'admin/class-mergado-marketing-pack-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */

        require_once plugin_dir_path(__DIR__) . 'public/class-mergado-marketing-pack-public.php';

        $this->loader = new Mergado_Marketing_Pack_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Mergado_Marketing_Pack_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Mergado_Marketing_Pack_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        /*******************************************************************************************************************
         * ADMIN - INIT BAR
         *******************************************************************************************************************/
        AdminBarService::getInstance()->init();

        /*******************************************************************************************************************
         * ADMIN - INIT - NEWS NOTICE
         *******************************************************************************************************************/
        AdminNoticeService::getInstance()->initNotices();

        /*******************************************************************************************************************
         * WP_CRON - ADD CUSTOM MERGADO SCHEDULE and ACTIONS
         *******************************************************************************************************************/
        CronScheduleService::getInstance()->initSchedules();
        CronActionService::getInstance()->initActions();

        /*******************************************************************************************************************
         * ADMIN - PLUGIN - SET PLUGIN LINKS
         *******************************************************************************************************************/
        PluginPageSettingsService::getInstance()->initSettings();

        /*******************************************************************************************************************
         * ADMIN - POST ENDPOINTS
         *******************************************************************************************************************/
        add_action('init', function() {
            ApiService::getInstance()->initAdminEndpoints();
        });

        /**
         * LOADERS
         */

        $plugin_admin = new Mergado_Marketing_Pack_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'setDefaultEan');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Mergado_Marketing_Pack_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Init public endpoints
        add_action('wp_loaded', function() {
            ApiService::getInstance()->initPublicEndpoints();
        });
    }

    /**
     * Run the loader to execute all the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
        $this->initServices();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Mergado_Marketing_Pack_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    public function initServices()
    {
        //Data to templates
        add_action("woocommerce_after_shop_loop_item", [$this, "productListData"], 99);
        add_filter("woocommerce_blocks_product_grid_item_html", [$this, "productListDataFilter"], 99, 3);

	    /**
	     * ADVERTISEMENT
	     */

        // GLAMI
        add_action( 'wp_head', [ $this->glamiPixelServiceIntegration, 'init' ], 99 );
        add_action( 'wp_head', [ $this->glamiPixelServiceIntegration, 'addToCartAjax' ], 99 );
        add_action('woocommerce_add_to_cart', function() {
            // Add items to head
            $this->headerExtra .= $this->glamiPixelServiceIntegration->addToCart();
        }, 99);

        // BIANO
        add_action( 'woocommerce_add_to_cart', function() {
           $this->headerExtra .= $this->bianoServiceIntegration->addToCart();
        }, 99 );

        // FB PIXEL
        add_action("woocommerce_before_checkout_billing_form", [$this->facebookServiceIntegration, "initiateCheckout"], 99);
        add_action('woocommerce_add_to_cart', function() {
            $this->headerExtra .= $this->facebookServiceIntegration->addToCart();
        }, 99);

	    /**
	     * ANALYTICS
	     */

        // GTAG
        add_action( 'woocommerce_after_shipping_rate', [$this->ga4ServiceIntegration, "actionShippingRate"], 99);

        add_action("woocommerce_after_cart", [$this->ga4ServiceIntegration, "addCartData"], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this->ga4ServiceIntegration, "addCartData"], 99);

        add_action("woocommerce_after_cart", [$this->googleUniversalAnalyticsServiceIntegration, 'removeFromCart'], 99);
        add_action("woocommerce_after_cart", [$this->ga4ServiceIntegration, 'removeFromCart'], 99);
        add_action("woocommerce_after_cart", [$this->ga4ServiceIntegration, 'viewCart'], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this->googleUniversalAnalyticsServiceIntegration, "checkoutStep"], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this->googleUniversalAnalyticsServiceIntegration, "checkoutManipulation"], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this->ga4ServiceIntegration, "beginCheckout"], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this->ga4ServiceIntegration, "addPaymentInfo"], 99);
        add_action("woocommerce_before_checkout_billing_form", [$this->ga4ServiceIntegration, "addShippingInfo"], 99);
        add_action("woocommerce_after_cart", [$this->googleUniversalAnalyticsServiceIntegration, "checkoutManipulation"], 99);
        add_action("woocommerce_after_cart", [$this->ga4ServiceIntegration, "addShippingInfo"], 99);
        add_action("woocommerce_after_cart", [$this->ga4ServiceIntegration, "addPaymentInfo"], 99);



        add_action('woocommerce_after_single_product', [$this->googleAdsServiceIntegration, 'productDetailView'], 98); // GDPR resolved inside
        add_action('woocommerce_after_single_product', [$this->googleUniversalAnalyticsServiceIntegration, 'productDetailView'], 98); // GDPR resolved inside
        add_action('woocommerce_after_single_product', [$this->ga4ServiceIntegration, 'productDetailView'], 98); // GDPR resolved inside
        add_action('woocommerce_add_to_cart', function() {
            $this->headerExtra .= $this->googleAdsServiceIntegration->addToCart();
        }, 99); // GDPR resolved inside

        add_action('woocommerce_add_to_cart', function() {
            $this->headerExtra .= $this->googleUniversalAnalyticsServiceIntegration->addToCart();
        }, 99); // GDPR resolved inside

        add_action('woocommerce_add_to_cart', function() {
            $this->headerExtra .= $this->ga4ServiceIntegration->addToCart();
        }, 99); // GDPR resolved inside

        add_action( "wp_footer", [ $this->googleAdsServiceIntegration, "viewItemList"], 99 ); // GDPR resolved inside
        add_action( "wp_footer", [ $this->googleUniversalAnalyticsServiceIntegration, "viewItemList"], 99 ); // GDPR resolved inside
        add_action( "wp_footer", [ $this->ga4ServiceIntegration, "viewItemList"], 99 ); // GDPR resolved inside
        add_action( "wp_footer", [ $this->ga4ServiceIntegration, "search"], 99 ); // GDPR resolved inside

        // GTM
        add_action('woocommerce_add_to_cart', function() {
            $this->headerExtra .= $this->googleTagManagerServiceIntegration->addToCart();
        }, 99);

        add_action("woocommerce_before_checkout_billing_form", [$this->googleTagManagerServiceIntegration, "checkoutManipulation"], 99);
        add_action("woocommerce_after_cart", [$this->googleTagManagerServiceIntegration, "checkoutManipulation"], 99);
        add_action("wp_footer", [$this->googleTagManagerServiceIntegration, "viewList"], 99);


        // GA refund - backend - not part of GDPR
        add_action('woocommerce_order_fully_refunded', [$this, 'refundFull']);
        add_action('woocommerce_order_status_changed', [$this, 'orderStatusChanged']);

        // Ga4 REFUND
        add_action('woocommerce_order_fully_refunded', [$this->ga4ServiceIntegration, 'refundFull'], 10, 2);
        add_action('woocommerce_order_partially_refunded', [$this->ga4ServiceIntegration, 'refundPartial'], 10, 2);
        add_action('woocommerce_order_status_changed', [$this->ga4ServiceIntegration, 'orderStatusChanged'], 10, 5);
        add_action('admin_head', [$this->ga4ServiceIntegration, 'insertHeaderAdmin']);
        add_action('admin_head', [$this->ga4ServiceIntegration, 'pushRefundEvent']);

        //Checkout steps/options - checkboxs - not part of GDPR
        add_action("woocommerce_review_order_before_submit", [$this->heurekaServiceIntegration, "addVerifyOptOutCheckbox"], 10);
        add_action("woocommerce_review_order_before_submit", [$this->zboziServiceIntegration, "addCheckboxVerifyOptIn"], 10);
        add_action("woocommerce_review_order_before_submit", [$this->arukeresoServiceIntegration, "addCheckboxVerifyOptOut"], 10);
        add_action("woocommerce_review_order_before_submit", [$this->compariServiceIntegration, "addCheckboxVerifyOptOut"], 10);
        add_action("woocommerce_review_order_before_submit", [$this->pazaruvajServiceIntegration, "addCheckboxVerifyOptOut"], 10);
        add_action("woocommerce_review_order_before_submit", [$this->bianoStarServiceIntegration, "addCheckboxOptOut"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this->heurekaServiceIntegration, "setOrderMetaData"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this->zboziServiceIntegration, "setOrderMetaData"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this->bianoStarServiceIntegration, "setOrderMeta"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this->arukeresoServiceIntegration, "setOrderMetaData"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this->compariServiceIntegration, "setOrderMetaData"], 10);
        add_action("woocommerce_checkout_update_order_meta", [$this->pazaruvajServiceIntegration, "setOrderMetaData"], 10);

        //add_action('woocommerce_after_single_product', [$this, 'adWordsRemarketingProduct'], 99); TODO: delete function in future
        //add_action('woocommerce_after_cart', [$this, 'adsRemarketingCart'], 99); TODO: delete function in future
        //add_action('woocommerce_after_single_product', [$this, 'googleTagManagerProductDetailView'], 98);
        //add_action("woocommerce_before_checkout_billing_form", [$this, "googleTagManager_checkout_step"], 99);
        //add_action("woocommerce_after_checkout_billing_form", [$this, "checkout_carrier_set"], 99); // not possible
        //add_action("woocommerce_after_checkout_billing_form", [$this, "checkout_payment_set"], 99); // not possible
        //add_action('woocommerce_order_partially_refunded', [$this, 'refundPartial']); // Disabled for now

        // Multi
        add_action('wp_head', [$this, 'mergadoHeaderSetup'], 99);
        add_action('wp_footer', [$this, 'mergadoFooterSetup'], 98);
        add_action('woocommerce_thankyou', [$this, 'mergadoOrderConfirmed'], 99);

        if (function_exists( 'wp_body_open' ) ) {
            add_action('wp_body_open', [$this, 'afterBodyOpeningTag']);
        }
    }

    // Make full refund based on selected statuses
    public function orderStatusChanged($orderId)
    {
        $order = wc_get_order($orderId);
        $alreadyRefunded = $order->get_meta('orderFullyRefunded-' . $orderId, true);

        $GaUniversalService = GaUniversalService::getInstance();
        $GaRefundClass = GoogleAnalyticsRefundService::getInstance();

        if ($GaUniversalService->isActiveEcommerce()) {
            if ($GaRefundClass->isStatusActive($_POST['order_status'])) {

	            // Check if backend data already sent
	            if (empty($alreadyRefunded)) {
                    $order->update_meta_data('orderFullyRefunded-' . $orderId, 1);
                    $order->save();

		            $GaRefundClass->sendRefundCode( [], $orderId, false );
	            }
            }
        }
    }

    // Only available for WP 5.2+
    // Used on two places (if not available, then in footer)
    function afterBodyOpeningTag() {
        $this->googleTagManagerServiceIntegration->mainCodeAfterBody();
    }


    // Can't be commented and replaced with orderStatusChanged, because some situations in partial refund ends with this
    public function refundFull($orderId)
    {
        //Change status to refunded or if all prices filled when clicked refund button
	    $GaRefundClass = GoogleAnalyticsRefundService::getInstance();
        $GaUniversalService = GaUniversalService::getInstance();

	    if ($GaUniversalService->isActiveEcommerce()) {
            $order = wc_get_order($orderId);
            $alreadyRefunded = $order->get_meta('orderFullyRefunded-' . $orderId, true);

		    if (empty($alreadyRefunded)) {
                $order->update_meta_data('orderFullyRefunded-' . $orderId, 1);
                $order->save();

                $GaRefundClass->sendRefundCode([], $orderId,  false);
		    }
	    }
    }

    // Refund only whole items.. not if lower price
    public function refundPartial($orderId)
    {
        $GaRefundClass = GoogleAnalyticsRefundService::getInstance();
        $GaUniversalService = GaUniversalService::getInstance();

        if ($GaUniversalService->isActiveEcommerce()) {
            $data = json_decode(stripslashes( $_POST['line_item_qtys']));

            $products = [];

            foreach ($data as $id => $quantity) {
                $productId = wc_get_order_item_meta( $id, '_product_id', true );
                $variationId = wc_get_order_item_meta( $id, '_variation_id', true );
                if ($variationId != 0) {
                    $id = $productId . '-' . $variationId;
                } else {
                    $id = $productId;
                }

                $products[$id] = $quantity;
            }

            // Check if products are empty ==> (products not refunded.. just discounted)
            if (!empty($products)) {
                $GaRefundClass->sendRefundCode($products, $orderId,  true);
            }
        }
    }

    public function productListDataFilter($html, $data, $product): string
    {
        $productMetadataString = $this->getProductListFormattedMetadata($product) . '</li>';

        return str_replace('</li>', $productMetadataString, $html);
    }

    public function productListData()
    {
        global $product;

        echo $this->getProductListFormattedMetadata($product);
    }

    public function getProductListFormattedMetadata($product): string
    {
        $productData = $this->getProductListData($product);
        return "<div data-metadata-product-list='" . htmlspecialchars(json_encode($productData, JSON_NUMERIC_CHECK), ENT_QUOTES) . "'></div>";
    }

    public function getProductListData($product)
    {
        $category = get_the_terms($product->get_id(), "product_cat");
        $categoriesArray = [];

        if ($category) {
            foreach ($category as $term) {
                $categoriesArray[] = $term->name;
            }
        }

        $categories = implode(', ', $categoriesArray);

        $productData = [];

        $productData['base_id'] = $product->get_id();

        if (!$product->is_type('variable')) {
            $productData['full_id'] = $product->get_id();
            $productData['has_variation'] = false;
        } else {
            $productData['variation_id'] = $product->get_id();
            $productData['full_id'] = $product->get_id() . '-' . $product->get_id(); // Product can't be shown in specific variation so its always like 11 - 11
            $productData['has_variation'] = true;
        }

        $priceWithVat = (float) wc_get_price_including_tax($product);
        $priceWithoutVat = (float) wc_get_price_excluding_tax($product);
        $regularPriceWithVat = (float) wc_get_price_including_tax($product, ['price' => $product->get_regular_price()]);
        $regularPriceWithoutVat = (float) wc_get_price_excluding_tax($product, ['price' => $product->get_regular_price()]);
        $discountWithVat = $priceWithVat !== $regularPriceWithVat ? $regularPriceWithVat - $priceWithVat : 0;
        $discountWithoutVat = $priceWithoutVat !== $regularPriceWithoutVat ? $regularPriceWithoutVat - $priceWithoutVat : 0;

        $productData['name'] = $product->get_name();
        $productData['category'] = $categories;
        $productData['categories_json'] = $categoriesArray;
        $productData['price'] = $product->get_price();
        $productData['price_with_vat'] = $priceWithVat;
        $productData['price_without_vat'] = $priceWithoutVat;
        $productData['regular_price_with_vat'] = $regularPriceWithVat;
        $productData['regular_price_without_vat'] = $regularPriceWithoutVat;
        $productData['discount_with_vat'] = $discountWithVat;
        $productData['discount_without_vat'] = $discountWithoutVat;
        $productData['currency'] = get_woocommerce_currency();

        return $productData;
    }

    /*******************************************************************************************************************
     * ORDER CONFIMARTION - NAJNAKUP, PRICEMANIA, ZBOZI.CZ, ADWORDS, SKLIK, FBPIXEL, GLAMI
     *******************************************************************************************************************/

    /**
     * Najnakup integration
     *
     * @param $orderId
     */
    public function mergadoOrderConfirmed($orderId)
    {
        $order = wc_get_order($orderId);
        $confirmed = $order->get_meta('orderConfirmed', true);

        // Check if backend data already sent
        if (empty($confirmed) || MERGADO_DEBUG) {
            $googleReviewsClass = GoogleReviewsService::getInstance();
            $order->update_meta_data('orderConfirmed', 1);
            $order->save();

            $this->zboziServiceIntegration->submitOrderToZbozi($orderId);
            $this->heurekaServiceIntegration->submitVerify($orderId);
            $this->najNakupServiceIntegration->sendValuation($orderId);
            $this->pricemaniaServiceIntegration->sendOverenyObchod($orderId);
            $googleReviewsClass->getOptInTemplate($order);

            $this->arukeresoServiceIntegration->orderConfirmation($orderId);
            $this->compariServiceIntegration->orderConfirmation($orderId);
            $this->pazaruvajServiceIntegration->orderConfirmation($orderId);
            $this->facebookServiceIntegration->purchased($orderId);
            $this->googleAdsServiceIntegration->conversion($orderId);
            $this->argepServiceIntegration->conversion($orderId);

            if($this->cookieService->advertisementEnabled()) {
                $this->heurekaServiceIntegration->conversion($orderId);
                $this->glamiTopServiceIntegration->purchase($orderId);
                $this->kelkooServiceIntegration->kelkooPurchase($orderId);
            }

            $this->bianoServiceIntegration->purchase($orderId);

            $this->glamiPixelServiceIntegration->purchased($orderId);
            $this->zboziServiceIntegration->conversion($orderId);
            $this->sklikServiceIntegration->conversion($orderId); // GDPR got custom integration in platform
            $this->googleUniversalAnalyticsServiceIntegration->purchased($orderId);
            $this->ga4ServiceIntegration->purchase($orderId);
        }
    }

    public function mergadoHeaderSetup()
    {
        $this->createJsVariables();

        $this->bianoServiceIntegration->header();

        $this->gtagIntegration->insertHeader();  // GDPR resolved inside

        $this->googleTagManagerServiceIntegration->initDataLayer();
        $this->googleTagManagerServiceIntegration->productDetailView(); // must be before GTM

        if (is_order_received_page()) {
            $orderId = empty($_GET['order']) ? ($GLOBALS['wp']->query_vars['order-received'] ? $GLOBALS['wp']->query_vars['order-received'] : 0) : absint($_GET['order']);

            $orderId_filter = apply_filters('woocommerce_thankyou_order_id', $orderId);

            if ($orderId_filter != '') {
                $orderId = $orderId_filter;
            }

            $order = wc_get_order($orderId);
            $confirmed = $order->get_meta('orderConfirmed', true);

            // Check if backend data already sent
            if (empty($confirmed) || MERGADO_DEBUG) {
                $this->googleTagManagerServiceIntegration->transaction();
                $this->googleTagManagerServiceIntegration->purchase();
            }
        }

        $this->googleTagManagerServiceIntegration->checkoutStep();
        $this->googleTagManagerServiceIntegration->mainCodeHead();
    }


    /*******************************************************************************************************************
     * FOOTER SETUP - SKLIK, ADWORDS, ETARGET
     *******************************************************************************************************************/

    public function mergadoFooterSetup($orderId)
    {
        $googleReviewsClass = GoogleReviewsService::getInstance();

        echo '<div id="mergadoSetup" data-currency="' . get_woocommerce_currency() . '"></div>';

        //Should be after body tag but not available in old (< 5.2) versions ... fallback
        if ( ! function_exists( 'wp_body_open' ) ) {
            $this->afterBodyOpeningTag();
        }

        $this->facebookServiceIntegration->init(); // GDPR managed inside own logic
        $this->facebookServiceIntegration->addToCartAjax();

	    if ($this->cookieService->advertisementEnabled()) {
		    $this->etargetServiceIntegration->etargetRetarget();
	    }

        $this->bianoServiceIntegration->addToCartAjax();

        $this->glamiPixelServiceIntegration->viewContent();
        $this->sklikServiceIntegration->retargeting();

        $googleReviewsClass->getBadgeTemplate();
        $this->heurekaServiceIntegration->getWidgetTemplate();
        $this->arukeresoServiceIntegration->getWidgetTemplate();
        $this->compariServiceIntegration->getWidgetTemplate();
        $this->pazaruvajServiceIntegration->getWidgetTemplate();

        $this->googleUniversalAnalyticsServiceIntegration->removeFromCart();

        $this->googleTagManagerServiceIntegration->removeFromCartAjax();
        $this->googleTagManagerServiceIntegration->addToCartAjax();;

        $this->googleUniversalAnalyticsServiceIntegration->addToCartAjax(); // GDPR resolved inside
        $this->ga4ServiceIntegration->addToCartAjax(); // GDPR resolved inside
        $this->googleAdsServiceIntegration->addToCartAjax(); // GDPR resolved inside

        //Method that need to be called later (because of initializing their object)
        echo $this->headerExtra;
    }

    public function createJsVariables()
    {
        // Basic wrapper
        ?>
        <script>
            window.mmp = {};
        </script>
        <?php

        $this->cookieService->createJsVariables();
    }
}
