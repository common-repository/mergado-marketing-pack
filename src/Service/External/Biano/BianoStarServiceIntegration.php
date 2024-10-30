<?php

namespace Mergado\Service\External\Biano;

use Mergado\Helper\LanguageHelper;
use Mergado\Traits\SingletonTrait;

class BianoStarServiceIntegration
{

    use SingletonTrait;

    /**
     * @var BianoStarService
     */
    private $bianoStarService;

    /**
     * @var string
     */
    private $lang;

    public const BIANO_STAR_CHECKBOX = 'mmp_order_biano_star_checkbox';

    public function __construct()
    {
        $this->bianoStarService = BianoStarService::getInstance();
        $this->lang = LanguageHelper::getLang();
    }

    public function getService(): BianoStarService
    {
        return $this->bianoStarService;
    }

    public function addCheckboxOptOut(): void
    {
        if ($this->bianoStarService->isActive($this->lang)) {
            $lang = get_locale();
            $defaultText = stripslashes($this->bianoStarService->getOptOut('en_US'));
            $checkboxText = stripslashes($this->bianoStarService->getOptOut($lang));

            if (trim($checkboxText) === '') {
                $checkboxText = $defaultText;
            }

            if (trim($checkboxText) === '') {
                $checkboxText = BianoStarService::DEFAULT_OPT;
            }

            woocommerce_form_field(self::BIANO_STAR_CHECKBOX, array( // CSS ID
                'type' => 'checkbox',
                'class' => array('form-row' . ' ' . self::BIANO_STAR_CHECKBOX), // CSS Class
                'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
                'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
                'required' => false, // Mandatory or Optional
                'label' => $checkboxText,
            ));
        }
    }

    public function setOrderMeta($orderId): void
    {

        // Set to order meta if user want zbozi review email
        if (isset($_POST[self::BIANO_STAR_CHECKBOX]) && $_POST[self::BIANO_STAR_CHECKBOX]) {
            $order = wc_get_order($orderId);
            $order->update_meta_data(self::BIANO_STAR_CHECKBOX, esc_attr($_POST[self::BIANO_STAR_CHECKBOX]));
            $order->save();
        }
    }

    public function shouldBeSent($orderId): bool
    {
        if ($this->bianoStarService->isActive($this->lang)) {
            $order = wc_get_order($orderId);
            $confirmed = $order->get_meta(self::BIANO_STAR_CHECKBOX, true);

            if (empty($confirmed)) {
                return true;
            }
        }

        return false;
    }
}
