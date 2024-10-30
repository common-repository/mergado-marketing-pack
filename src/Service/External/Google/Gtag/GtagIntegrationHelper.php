<?php

namespace Mergado\Service\External\Google\Gtag;

use Mergado\Helper\AddToCartAjaxHelper;
use Mergado\Service\CookieService;
use Mergado\Service\External\Argep\ArgepService;
use Mergado\Service\External\Google\GoogleAds\GoogleAdsService;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\Ga4Service;
use Mergado\Service\External\Google\GoogleAnalytics\GA4\Ga4ServiceIntegration;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalService;
use Mergado\Service\External\Google\GoogleAnalytics\Universal\GaUniversalServiceIntegration;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;
use WC_Product;
use WpOrg\Requests\Cookie;

class GtagIntegrationHelper
{
    use SingletonTrait;

    /**
     * @var GaUniversalService
     */
    private $gaUniversalService;

    /**
     * @var GaUniversalServiceIntegration
     */
    private $gaUniversalServiceIntegration;
    /**
     * @var GoogleAdsService
     */
    private $googleAdsService;
    /**
     * @var Ga4Service
     */
    private $ga4Service;
    /**
     * @var Ga4ServiceIntegration
     */
    private $ga4ServiceIntegration;

    /**
     * @var ArgepService
     */
    private $argepService;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->gaUniversalService = GaUniversalService::getInstance();
        $this->ga4Service = Ga4Service::getInstance();
        $this->gaUniversalServiceIntegration = GaUniversalServiceIntegration::getInstance();
        $this->googleAdsService = GoogleAdsService::getInstance();
        $this->ga4ServiceIntegration = Ga4ServiceIntegration::getInstance();
        $this->argepService = ArgepService::getInstance();
        $this->cookieService = CookieService::getInstance();
    }

    public static function addToCart($sendTo, $withVat = null) : string
    {
        $result = '';

        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], AddToCartAjaxHelper::getDisabledActionNames(), true)) {
            return $result;
        }

        if (isset($_POST['add-to-cart'])) {
            $product = wc_get_product($_POST['add-to-cart']);

            if ($product->get_type() === 'grouped') { // Check if grouped product
                if (!isset($_POST['groupedGTAG'])) { // Check if request is duplicate (grouped products send two posts with same data)
                    $_POST['groupedGTAG'] = true; // Set variable that disable next call of same addToCart

                    foreach ($_POST['quantity'] as $id => $quantity) {
                        $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page
                        $categories = get_the_terms($id, 'product_cat');

                        $output = [];
                        if ($categories) {
                            foreach ($categories as $category) {
                                $output[] = $category->name;
                            }
                        }

                        $productCategories = join(", ", $output);

                        $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/addToCart.php';

                        $templateVariables = [
                            'currency' => get_woocommerce_currency(),
                            'itemsId' => $id,
                            'itemsName' => $product->get_name(),
                            'itemsCategory' => $productCategories,
                            'itemsQuantity' => $_POST['quantity'],
                            'itemsPrice' => self::getProductPrices($product, $withVat),
                            'itemsGoogleBusinessVertical' => 'retail',
                            'sendTo' => $sendTo,
                        ];

                        $result .= TemplateLoader::getTemplate($templatePath, $templateVariables);
                    }
                }
            } else {
                $product = wc_get_product($_POST['add-to-cart']);

                if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                    $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                } else {
                    $id = $product->get_data()['id'];
                }

                $categories = get_the_terms($id, 'product_cat');

                $output = [];
                if ($categories) {
                    foreach ($categories as $category) {
                        $output[] = $category->name;
                    }
                }

                $productCategories = implode(", ", $output);

                $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/addToCart.php';

                $templateVariables = [
                    'currency' => get_woocommerce_currency(),
                    'itemsId' => $id,
                    'itemsName' => $product->get_name(),
                    'itemsCategory' => $productCategories,
                    'itemsQuantity' => $_POST['quantity'],
                    'itemsPrice' => self::getProductPrices($product, $withVat),
                    'itemsGoogleBusinessVertical' => 'retail',
                    'sendTo' => $sendTo,
                ];

                $result .= TemplateLoader::getTemplate($templatePath, $templateVariables);
            }
        }

        return $result;
    }

    public static function productDetailView($sendTo, $withVat = null): void
    {
        if (is_product()) {
            $id = get_queried_object_id();
            $product = wc_get_product($id);

            $categories = get_the_terms($id, 'product_cat');

            $output = [];
            if ($categories) {
                foreach ($categories as $category) {
                    $output[] = $category->name;
                }
            }

            $productCategories = implode(", ", $output);

            $productData = [
                'id' => $id,
                'name' => $product->get_name(),
                'category' => $productCategories,
                'price' => self::getProductPrices($product, $withVat),
                'google_business_vertical' => 'retail',
            ];

            $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/viewItem.php';

            $templateVariables = [
                'currency' => get_woocommerce_currency(),
                'items' => json_encode($productData, JSON_NUMERIC_CHECK),
                'sendTo' => $sendTo
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);

            //If user come from my url === clicked on product url
            if (isset($_SERVER["HTTP_REFERER"])) {
                if (strpos($_SERVER["HTTP_REFERER"], get_site_url()) !== false) {

                    $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/selectContent.php';

                    $templateVariables = [
                        'currency' => get_woocommerce_currency(),
                        'contentType' => 'product',
                        'items' => json_encode($productData, JSON_NUMERIC_CHECK),
                        'sendTo' => $sendTo
                    ];

                    echo TemplateLoader::getTemplate($templatePath, $templateVariables);
                }
            }
        }
    }

    public static function addToCartAjax($sendTo, $withVat = null): void
    {
        $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/addToCartAjax.php';
        $templateVariables = [
            'withVat' => $withVat,
            'currency' => get_woocommerce_currency(),
            'sendTo' => $sendTo,
        ];

        echo TemplateLoader::getTemplate($templatePath, $templateVariables);
    }

    public static function viewItemList($sendTo, $withVat = null): void
    {
        if (is_shop()) {
            $listName = 'shop';
        } else if (is_product_category()) {
            $listName = get_queried_object()->name;
        } else if (is_search()) {
            $listName = 'search';
        } else {
            $listName = '';
        }

        $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/Universal/viewItemList.php';

        $templateVariables = [
            'withVat' => $withVat,
            'currency' => get_woocommerce_currency(),
            'listName' => $listName,
            'sendTo' => $sendTo,
        ];

        echo TemplateLoader::getTemplate($templatePath, $templateVariables);
    }

    public function insertHeader(): void
    {
        if ($this->cookieService->analyticalEnabled()) {
            $analyticalStorage = 'granted';
        } else {
            $analyticalStorage = 'denied';
        }

        if ($this->cookieService->advertisementEnabled()) {
            $advertisementStorage = 'granted';
        } else {
            $advertisementStorage = 'denied';
        }

        $gtagMainCode = '';

        //Google analytics
        $gaUniversalActive = $this->gaUniversalService->isActive();
        $ga4Active = $this->ga4Service->isActive();

        //Google ADS
        $googleAdsConversionsActive = $this->googleAdsService->isConversionActive();
        $googleAdsRemarketingActive = $this->googleAdsService->isRemarketingActive();

        //Argep
        $argepConversionsActive = $this->argepService->isConversionActive();

        //Primarily use code for analytics so no need for config on all functions
        if ($gaUniversalActive) {
            $gaMeasurementId = $this->gaUniversalServiceIntegration->getFormattedAnalyticsCode();

            $gtagMainCode = $gaMeasurementId;
            $gtagAnalyticsCode = $gaMeasurementId;
        }

        if ($ga4Active) {
            $ga4MeasurementId = $this->ga4ServiceIntegration->getFormattedAnalyticsCode();

            if ($gtagMainCode === '') {
                $gtagMainCode = $ga4MeasurementId;
            }

            $gtagAnalytics4Code = $ga4MeasurementId;
        }

        if ($googleAdsRemarketingActive || $googleAdsConversionsActive) {
            $googleAdsConversionCode = $this->googleAdsService->getConversionCode();

            if ($gtagMainCode === '') {
                $gtagMainCode = $googleAdsConversionCode;
            }
        }

        if ($argepConversionsActive) {
            $argepConversionCode = $this->argepService->getConversionCode();

            if ($gtagMainCode === '') {
                $gtagMainCode = $argepConversionCode;
            }
        }

        if (isset($gtagMainCode) && $gtagMainCode !== '') {
            $googleAdsData = ['show' => false];

            // Basic Gads code
            if (isset($googleAdsConversionCode) && $googleAdsConversionCode && $googleAdsRemarketingActive) {
                if ($this->cookieService->advertisementEnabled()) {
                    $googleAdsData = ['show' => true, 'code' => $googleAdsConversionCode, 'props' => []];
                } else {
                    $googleAdsData = ['show' => true, 'code' => $googleAdsConversionCode, 'props' => ['allow_ad_personalization_signals' => false]];
                }
            } elseif (isset($googleAdsConversionCode) && $googleAdsConversionCode) {
                $googleAdsData = ['show' => true, 'code' => $googleAdsConversionCode, 'props' => ['allow_ad_personalization_signals' => false]];
            }

            // Gads conversions
            if ($this->googleAdsService->getEnhancedConversionsActive()) {
                $googleAdsData['props']['allow_enhanced_conversions'] = true;
            }


            $templatePath = __MERGADO_SRC_DIR__ . 'Service/External/Google/Gtag/templates/header.php';

            $templateVariables = [
                'gtagMainCode' => $gtagMainCode,
                'gtagAnalyticsCode' => $gtagAnalyticsCode ?? false,
                'gtagAnalytics4Code' => $gtagAnalytics4Code ?? false,
                'analyticalStorage' => $analyticalStorage,
                'advertisementStorage' => $advertisementStorage,
                'googleAdsEnhancedConversionsActive' => $this->googleAdsService->getEnhancedConversionsActive(),
                'argepConversionCode' => $argepConversionCode ?? false,
                'googleAdsConversionCode' => $googleAdsConversionCode ?? false,
                'googleAdsRemarketingActive' => $googleAdsRemarketingActive,
                'googleAdsData' => $googleAdsData,
                'cookiesAdvertisementEnabled' => $this->cookieService->advertisementEnabled(),
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    /**
     * Helpers
     */

    public static function getProductPrices(WC_Product $product, $withVat = null): float
    {
        if ($withVat === null) {
            $price = $product->get_price(); //Default GA price
        } else if ($withVat) {
            $price = wc_get_price_including_tax($product); // Price after product discount
        } else {
            $price = wc_get_price_excluding_tax($product); // Price after product discount
        }

        return (float)$price;
    }
}
