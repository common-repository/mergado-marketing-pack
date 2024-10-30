<?php

/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Service\External\Glami;

use Mergado\Helper\LanguageHelper;
use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class GlamiTopServiceIntegration
{
    use SingletonTrait;

    private $glamiTopService;

    public function __construct()
    {
        $this->glamiTopService = GlamiTopService::getInstance();
    }

    public function purchase($orderId): void
    {
        $lang = LanguageHelper::getLang();
        $langISO = LanguageHelper::getLangIso();

        $active = $this->glamiTopService->isActive();
        $selection = $this->glamiTopService->getSelection();
        $code = $this->glamiTopService->getCode();

        if ($active && count($selection) > 0) {
            $domain = $selection['name'];

            $order = wc_get_order($orderId);
            $products_tmp = $order->get_items();

            $products = array();

            foreach ($products_tmp as $product) {
                if ($product->get_variation_id() == 0) {
                    $id = $product->get_data()['product_id'];
                } else {
                    $id = $product->get_variation_id();
                }

                $products[] = ['id' => $id, 'name' => $product->get_name()];
            }

            echo TemplateLoader::getTemplate(__DIR__ . '/templates/top/purchase.php',
                [
                    'domain' => $domain,
                    'merchantId' => $code,
                    'lang' => strtolower($lang),
                    'orderId' => $orderId,
                    'email' => $order->get_billing_email(),
                    'language' => strtolower($langISO),
                    'items' => json_encode($products)
                ]);
        }
    }
}
