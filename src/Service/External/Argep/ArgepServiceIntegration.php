<?php
declare(strict_types=1);

namespace Mergado\Service\External\Argep;

use Mergado;
use Mergado\Utils\TemplateLoader;

class ArgepServiceIntegration
{
    use Mergado\Traits\SingletonTrait;

    /**
     * @var ArgepService
     */
    private $argepService;

    public function __construct()
    {
        $this->argepService = ArgepService::getInstance();
    }

    public function conversion($order_id): void
    {
        $order = wc_get_order($order_id);

        $active = $this->argepService->isConversionActive();
        $code = $this->argepService->getConversionCode();
        $label = $this->argepService->getConversionLabel();

        $orderTotal = $order->get_total();

        if ($active) {
            $templatePath = __DIR__ . '/templates/conversion.php';

            $templateVariables = [
                'sendTo' => $code . '/' . $label,
                'value' => $orderTotal,
                'currency' => get_woocommerce_currency(),
                'transactionId' => $order_id,
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }
}
