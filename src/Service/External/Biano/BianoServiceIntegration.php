<?php

namespace Mergado\Service\External\Biano;

use Mergado\Helper\AddToCartAjaxHelper;
use Mergado\Helper\LanguageHelper;
use Mergado\Service\CookieService;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class BianoServiceIntegration
{

    use SingletonTrait;

    /**
     * @var BianoService
     */
    private $bianoService;

    /**
     * @var string
     */
    private $lang;

    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct()
    {
        $this->bianoService = BianoService::getInstance();
        $this->lang = LanguageHelper::getLang();
        $this->cookieService = CookieService::getInstance();
    }

    public function header()
    {
        $templatePathDefault = __DIR__ . '/templates/initDefault.php';
        $templatePathFallback = __DIR__ . '/templates/iInitFallback.php';

        $active = $this->bianoService->isActive($this->lang);
        $merchantId = $this->bianoService->getMerchantId($this->lang);

        if ($active) {
            // Default solution
            if (in_array($this->lang, BianoService::LANGUAGES)) {

                $templateVariables = [
                    'merchantId' => $merchantId,
                    'consent' => $this->cookieService->advertisementEnabled() ? 'true' : 'false',
                    'lang' => strtolower($this->lang)
                ];

                echo TemplateLoader::getTemplate($templatePathDefault, $templateVariables);

                // Fallback solution for other languages
            } else {
                echo TemplateLoader::getTemplate($templatePathFallback, []);
            }
            ?>

            <script>
              bianoTrack('init', '<?php echo $merchantId; ?>');

              <?php if(is_product()) { ?>
              bianoTrack('track', 'product_view', {id: '<?php echo wc_get_product()->get_id(); ?>'});
              <?php } else { ?>
              bianoTrack('track', 'page_view');
              <?php } ?>
            </script>

            <?php if (!$this->cookieService->advertisementEnabled()) { ?>
                <script>
                  window.mmp.cookies.sections.advertisement.functions.bianoPixel = function () {
                    bianoTrack('consent', true);
                  };
                </script>

                <?php
            }
        }
    }

    public function addToCart() : string
    {
        $templatePath = __DIR__ . '/templates/addToCart.php';

        $result = '';

        // Disable if woodmart theme because of incompatibility
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], AddToCartAjaxHelper::getDisabledActionNames(), true)) {
            return $result;
        }

        $currency = get_woocommerce_currency();

        $active = $this->bianoService->isActive($this->lang);

        if ($active) {
            if (isset($_POST['add-to-cart'])) {
                $product = wc_get_product($_POST['add-to-cart']);

                if ($product->get_type() === 'grouped') { // Check if grouped product
                    if (!isset($_POST['groupedBianoPixel'])) { // Check if request is duplicate (grouped products send two posts with same data)
                        $_POST['groupedBianoPixel'] = true; // Set variable that disable next call of same addToCart

                        foreach ($_POST['quantity'] as $id => $quantity) {
                            $product = wc_get_product($id); // No need for ID changing because only simple products can be added on grouped page

                            $templateVariables = [
                                'id' => $id,
                                'quantity' => $quantity,
                                'unit_price' => $product->get_price(),
                                'currency' => $currency,
                            ];

                            $result .= TemplateLoader::getTemplate($templatePath, $templateVariables);
                        }
                    }
                } else {
                    if (isset($_POST['variation_id']) && $_POST['variation_id'] && $_POST['variation_id'] !== '') {
                        $id = $product->get_data()['id'] . '-' . $_POST['variation_id'];
                    } else {
                        $id = $product->get_data()['id'];
                    }

                    $templateVariables = [
                        'id' => $id,
                        'quantity' => $_POST['quantity'],
                        'unit_price' => $product->get_price(),
                        'currency' => $currency,
                    ];

                    $result .= TemplateLoader::getTemplate($templatePath, $templateVariables);

                }
            }
        }

        return $result;
    }

    public function addToCartAjax()
    {
        $active = $this->bianoService->isActive($this->lang);

        if ($active) {

            $templatePath = __DIR__ . '/templates/addToCartAjax.php';

            echo TemplateLoader::getTemplate($templatePath, []);

            return true;
        }

        return false;
    }

    public function purchase($orderId)
    {
        $active = $this->bianoService->isActive($this->lang);
        $vatIncluded = $this->bianoService->isConversionWithVat();

        if ($active):
            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();
            $email = $order->get_billing_email();

            //Set prices with or without vat
            // Specification looks that `quantity * unit_price` should be order_total
            if ($vatIncluded) {
                $orderPrice = number_format((float)$order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
            } else {
                $orderPrice = number_format((float)$order->get_total() - $order->get_total_tax() - $order->get_shipping_total(), wc_get_price_decimals(), '.', '');
            }

            $products = array();
            foreach ($products_tmp as $product) {

                if ($product->get_variation_id() == 0) {
                    $id = $product->get_data()['product_id'];
                    $productObject = wc_get_product($product->get_data()['product_id']);
                } else {
                    $id = $product->get_data()['product_id'] . '-' . $product->get_variation_id();
                    $productObject = wc_get_product($product->get_variation_id());
                }

                $item = [
                    'id' => (string)$id,
                    'quantity' => (int)$product->get_quantity(),
                    'name' => $product->get_name(),
                ];

                if ($productObject->get_image_id()) {
                    $item['image'] = htmlspecialchars(wp_get_original_image_url($productObject->get_image_id()));
                }

                if ($vatIncluded) {
                    $item['unit_price'] = $product->get_total() + $product->get_total_tax();
                } else {
                    $item['unit_price'] = $product->get_total();
                }

                $products[] = $item;
            }

            // Biano star
            $bianoStarServiceIntegration = BianoStarServiceIntegration::getInstance();
            $bianoStarShouldeBeSent = $bianoStarServiceIntegration->shouldBeSent($orderId);

            $templatePath = __DIR__ . '/templates/purchase.php';

            $templateVariables = [
                'orderId' => $orderId,
                'order_price' => (float)$orderPrice,
                'currency' => $order->get_currency(),
                'items' => json_encode($products, JSON_NUMERIC_CHECK),
                'bianoStarShouldBeSent' => $bianoStarShouldeBeSent
            ];


            if ($bianoStarShouldeBeSent) {
                $bianoStarService = $bianoStarServiceIntegration->getService();

                $shippingDate = 0;

                foreach ($products_tmp as $orderProduct) {
                    if ($product->get_variation_id() == 0) {
                        $product = wc_get_product($orderProduct->get_data()['product_id']);
                    } else {
                        $product = wc_get_product($orderProduct->get_data()['product_id']);
                    }

                    $productStatus = $product->get_stock_status();

                    if ($productStatus === 'instock') {
                        if ($shippingDate < $bianoStarService->getShipmentInStock()) {
                            $shippingDate = $bianoStarService->getShipmentInStock();
                        }
                    } else if ($productStatus === 'outofstock') {
                        if ($shippingDate < $bianoStarService->getShipmentOutOfStock()) {
                            $shippingDate = $bianoStarService->getShipmentOutOfStock();
                        }
                    } else if ($productStatus === 'onbackorder') {
                        if ($shippingDate < $bianoStarService->getShipmentBackorder()) {
                            $shippingDate = $bianoStarService->getShipmentBackorder();
                        }
                    }
                }

                $templateVariables['email'] = $email;
                $templateVariables['shippingDate'] = Date('Y-m-d', strtotime('+' . $shippingDate . ' days'));;
            }

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);

        endif;
    }
}
