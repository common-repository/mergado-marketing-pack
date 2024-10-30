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

namespace Mergado\Service\External\Pricemania;

use Exception;
use Mergado\Service\LogService;
use Mergado\Traits\SingletonTrait;

class PricemaniaServiceIntegration
{
    use SingletonTrait;

    /**
     * @var PricemaniaService
     */
    private $pricemaniaService;

    public function __construct()
    {
        $this->pricemaniaService = PricemaniaService::getInstance();
    }

    /**
     * Send data from backend to Pricemania
     */
    public function sendOverenyObchod($orderId): bool
    {
        $active = $this->pricemaniaService->isActive();
        $id = $this->pricemaniaService->getId();

        if ($active) {
            try {
                $order = wc_get_order($orderId);
                $email = $order->get_billing_email();
                $products = $order->get_items();

                $pricemania = new PricemaniaObject($id);

                foreach ($products as $product) {
                    $name = $product->get_name();
                    $pricemania->addProduct($name);
                }

                $pricemania->setOrder(array(
                    'email' => $email,
                    'orderId' => $orderId
                ));

                $pricemania->send();
                return true;
            } catch (Exception $e) {
                $logger = LogService::getInstance();
                $logger->error('[PRICEMANIA]: Error ' . $e->getMessage() . ' [ User probably has bad code in settings ]');
            }
        }

        return false;
    }
}
