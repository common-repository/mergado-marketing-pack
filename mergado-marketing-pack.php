<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.mergado.cz
 * @since             1.0.0
 * @package           Mergado_Marketing_Pack
 *
 * @wordpress-plugin
 * Plugin Name:       Mergado marketing pack
 * Plugin URI:        https://www.mergado.cz
 * Description:       Earn more on price comparator sites. <strong>REQUIRES: Woocommerce</strong>
 * Version:           3.7.4
 * Author:            Mergado technologies, s. r. o.
 * Author URI:        https://www.mergado.cz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mergado-marketing-pack
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 8.2.1
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Mergado\Exception\CronRunningException;
use Mergado\Feed\Category\CategoryFeed;
use Mergado\Feed\Customer\CustomerFeed;
use Mergado\Feed\Product\ProductFeed;
use Mergado\Feed\Stock\StockFeed;
use Mergado\Manager\TokenManager;
use Mergado\Service\AlertService;
use Mergado\Helper\LanguageHelper;
use Mergado\Service\ProductPriceImportService;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('PLUGIN_VERSION', '3.7.4');
define('WOOCOMMERCE_DEPENCENCY_MESSAGE', __('Mergado Pack plugin requires <a href="' . admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce') . '" target="_top">WooCommerce</a> plugin to be active!', 'mergado-marketing-pack'));
define( '__MERGADO_DIR__', plugin_dir_path( __FILE__ ) );
define( '__MERGADO_BASE_FILE__', plugin_dir_path( __FILE__ ) . 'mergado-marketing-pack.php' );
define( '__MERGADO_MMP_UPLOAD_DIR__', wp_get_upload_dir()['basedir'] . '/mmp/' );
define( '__MERGADO_TMP_DIR__', wp_get_upload_dir()['basedir'] . '/mergado/tmp/' );
define( '__MERGADO_XML_DIR__', wp_get_upload_dir()['basedir'] . '/mergado/data/' );
define( '__MERGADO_XML_URL__', wp_get_upload_dir()['baseurl'] . '/mergado/data/' );
define( '__MERGADO_SRC_DIR__', __MERGADO_DIR__ . 'src/' );
define( '__MERGADO_MIGRATIONS_DIR__', __MERGADO_DIR__ . 'migrations/');
define( '__MERGADO_ADMIN_IMAGES_DIR__', __MERGADO_DIR__ . 'admin/img/');
define( '__MERGADO_TEMPLATE_DIR__', __MERGADO_DIR__ . 'admin/templates/partials/');
define( '__MERGADO_TEMPLATE_COMPONENTS_DIR__', __MERGADO_DIR__ . 'admin/templates/partials/components/');

if (!defined( 'MERGADO_DEBUG' )) {
    define('MERGADO_DEBUG', false);
}

include_once __MERGADO_DIR__ . 'vendor/autoload.php';

// HPOS compatibility statement
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( FeaturesUtil::class ) ) {
        FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

add_action('init', function() {
    $domain = 'mergado-marketing-pack';

    $locale = LanguageHelper::getLocale();

    $locale = apply_filters( 'plugin_locale', $locale, $domain );

    unload_textdomain( $domain );
    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
    load_plugin_textdomain( $domain, false, plugin_basename( dirname( WC_PLUGIN_FILE ) ) . '/i18n/languages' );

    // Called always .. transient me if i am making problems
    $alertService = AlertService::getInstance();
    $alertService->checkIfErrorsShouldBeActive();
});

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mergado-marketing-pack-activator.php
 */
function activate_mergado_marketing_pack() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mergado-marketing-pack-activator.php';
    Mergado_Marketing_Pack_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mergado-marketing-pack-deactivator.php
 */
function deactivate_mergado_marketing_pack() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mergado-marketing-pack-deactivator.php';
    Mergado_Marketing_Pack_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_mergado_marketing_pack');
register_deactivation_hook(__FILE__, 'deactivate_mergado_marketing_pack');

// Direction browser CRON actions
add_action('wp_loaded', function() {
    $action = $_GET['action'] ?? '';
    $token = $_GET['token'] ?? '';

    if ($action !== '' && $token !== '' && home_url() . '/mergado/' === add_query_arg(array('action' => NULL, 'token' => NULL), home_url('/mergado/'))) {
        if ($token != TokenManager::getToken()) {
            echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: red; margin-right: 8px;"></span>';
            _e('ERROR: Invalid token', 'mergado-marketing-pack');
            exit;
        } else {
            // Disable Jetpack image url transformation (Jetpack_Photon) if exists and cron called
            if (is_plugin_active( 'jetpack/jetpack.php') && class_exists('Jetpack_Photon')) {
                remove_filter('image_downsize', [Jetpack_Photon::instance(), 'filter_image_downsize'], 10);
            }
        }

        try {
            switch ($action) {
                case 'productCron':
                    $productFeed = new ProductFeed();
                    $productFeed->generateXml();
                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Product feed generated', 'mergado-marketing-pack');
                    exit;

                case 'stockCron':
                    $stockFeed = new StockFeed();
                    $stockFeed->generateXML();

                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Heureka availability feed generated', 'mergado-marketing-pack');
                    exit;
                case 'categoryCron':
                    $categoryFeed = new CategoryFeed();
                    $categoryFeed->generateXML();
                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Category feed generated', 'mergado-marketing-pack');
                    exit;
                case 'customerCron':
                    $customerFeed = new CustomerFeed();
                    $customerFeed->generateXML();
                    echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                    _e('SUCCESS: Customer feed generated', 'mergado-marketing-pack');
                    exit;
                case 'importPrices':
                    $result = ProductPriceImportService::getInstance()->importPrices('');

                    if($result) {
                        echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: green; margin-right: 8px;"></span>';
                        _e('SUCCESS: Mergado prices imported', 'mergado-marketing-pack');
                    } else {
                        echo '<span style="display: inline-block; width: 14px; height: 14px; border-radius: 100%; background: red; margin-right: 8px;"></span>';
                        _e('ERROR: Error importing prices. Do you have correct URL in settings?', 'mergado-marketing-pack');
                    }
                    exit;
            }
        } catch (CronRunningException $e) {
            echo __('The cron is probably already running. Please try again later.');
            exit;
        } catch (Exception $e) {
            echo '<h2>' . __('An error occurred during cron run.') . '</h2><br>';
            echo '<br>';
            echo '<strong>' . __('If your problem persist, send following error to our support with your message.') . '</strong><br>';
            echo '<div class="mmpErrorCode" style="border: 1px solid black; padding: 13px; background-color: #ffffec; width: 1000px; max-width: 100%;">' . $e . '</div>';
            exit;
        }
    }
});



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-mergado-marketing-pack.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mergado_marketing_pack() {
    $plugin = new Mergado_Marketing_Pack();
    $plugin->run();
}

run_mergado_marketing_pack();
