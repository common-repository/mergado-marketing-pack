<?php

namespace Mergado\Service\External\ArukeresoFamily;

use Exception;
use Mergado\Service\External\ArukeresoFamily\Arukereso\ArukeresoService;
use Mergado\Service\External\ArukeresoFamily\Compari\CompariService;
use Mergado\Service\External\ArukeresoFamily\Pazaruvaj\PazaruvajService;
use Mergado\Service\LogService;
use Mergado\Utils\TemplateLoader;

abstract class AbstractArukeresoFamilyServiceIntegration
{

    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    // Set to order meta if user want zbozi review email
    public function setOrderMetaData($orderId): void
    {
        if (isset($_POST[$this->service::FRONTEND_CHECKBOX]) && $_POST[$this->service::FRONTEND_CHECKBOX]) {
            $order = wc_get_order($orderId);
            $order->update_meta_data($this->service::FRONTEND_CHECKBOX, esc_attr($_POST[$this->service::FRONTEND_CHECKBOX]));
            $order->save();
        }
    }

    public function addCheckboxVerifyOptOut(): void
    {
        if ($this->service->isActive()) {
            $lang = get_locale();
            $defaultText = stripslashes($this->service->getOptOut('en_US'));
            $checkboxText = stripslashes($this->service->getOptOut($lang));

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if ($checkboxText === 0 || trim($checkboxText) === '') {
                $checkboxText = $this->service::DEFAULT_OPT;
            }

            woocommerce_form_field($this->service::FRONTEND_CHECKBOX, array( // CSS ID
                'type' => 'checkbox',
                'class' => array('form-row', $this->service::FRONTEND_CHECKBOX), // CSS Class
                'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required' => false, // Mandatory or Optional
                'label' => $checkboxText,
            ));
        }
    }

    /*******************************************************************************************************************
     * GET TEMPLATE
     ******************************************************************************************************************/
    public function getWidgetTemplate(): void
    {
        if ($this->service->isWidgetActive()) {

            $templatePath = __DIR__ . '/templates/widget.php';

            $templateVariables = [
                "WEB_API_KEY" => $this->service->getWebApiKey(),
                "DESKTOP_POSITION" => $this->service::DESKTOP_POSITIONS()[$this->service->getWidgetDesktopPosition()]['value'],
                "MOBILE_POSITION" => $this->service::getMobilePositionsConstant()[$this->service->getWidgetMobilePosition()]['value'],
                "MOBILE_WIDTH" => $this->service->getWidgetMobileWidth(),
                "APPEARANCE_TYPE" => $this->service::APPEARANCE_TYPES()[$this->service->getWidgetAppearanceType()]['value']
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }

    /*******************************************************************************************************************
     * FUNCTIONS
     ******************************************************************************************************************/

    public function orderConfirmation($orderId): void
    {
        $order = wc_get_order($orderId);
        $confirmed = $order->get_meta($this->service::FRONTEND_CHECKBOX, true);

        if (empty($confirmed) && $this->service->isActive()) {
            $products = [];

            foreach ($order->get_items() as $item) {
                if ($item->get_data()['variation_id'] == 0) {
                    $id = $item->get_data()['product_id'];
                } else {
                    $id = $item->get_data()['product_id'] . '-' . $item->get_data()['variation_id'];
                }

                $name = $item->get_name();

                /** Assign product to array */
                $products[$id] = $name;
            }

            try {
                /** Provide your own WebAPI key. You can find your WebAPI key on your partner portal. */
                $Client = new TrustedShop($this->service->getWebApiKey(), $this->service::SERVICE_URL_SEND);

                /** Provide the e-mail address of your customer. You can retrieve the e-amil address from the webshop engine. */
                $Client->SetEmail($order->get_billing_email());

                /** Customer's cart example. */
                $Cart = $products;

                /** Provide the name and the identifier of the purchased products.
                 * You can get those from the webshop engine.
                 * It must be called for each of the purchased products. */
                foreach ($Cart as $ProductIdentifier => $ProductName) {
                    /** If both product name and identifier are available, you can provide them this way: */
                    $Client->AddProduct($ProductName, $ProductIdentifier);
                    /** If neither is available, you can leave out these calls. */
                }

                /** This method perpares to send us the e-mail address and the name of the purchased products set above.
                 *  It returns an HTML code snippet which must be added to the webshop's source.
                 *  After the generated code is downloaded into the customer's browser it begins to send purchase information. */
                echo $Client->Prepare();
                /** Here you can implement error handling. The error message can be obtained in the manner shown below. This step is optional. */
            } catch (Exception $e) {
                $logger = LogService::getInstance();

                $errorMessage = 'ArukeresoFamilyServiceIntegration error: ';

                if ($this->service instanceof CompariService) {
                    $errorMessage = '[Compari]: Order confirmation error: ';
                } else if ($this->service instanceof PazaruvajService) {
                    $errorMessage = '[Pazaruvaj]: Order confirmation error: ';
                } else if ($this->service instanceof ArukeresoService) {
                    $errorMessage = '[Arukereso]: Order confirmation error: ';
                }

                $logger->error($errorMessage);
                $logger->error($e);
            }
        }
    }
}
