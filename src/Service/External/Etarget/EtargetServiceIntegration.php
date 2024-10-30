<?php

namespace Mergado\Service\External\Etarget;


use Mergado\Traits\SingletonTrait;
use Mergado\Utils\TemplateLoader;

class EtargetServiceIntegration
{

    use SingletonTrait;

    /**
     * @var EtargetService
     */
    private $etargetService;

    public function __construct()
    {
        $this->etargetService = EtargetService::getInstance();
    }

    public function etargetRetarget(): void
    {
        $active = $this->etargetService->isActive();

        if ($active) {
            $templatePath = __DIR__ . '/templates/retarget.php';

            $templateVariables = [
                'id' => $this->etargetService->getId(),
                'hash' => $this->etargetService->getHash(),
            ];

            echo TemplateLoader::getTemplate($templatePath, $templateVariables);
        }
    }
}
