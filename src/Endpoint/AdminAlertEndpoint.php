<?php declare(strict_types=1);

namespace Mergado\Endpoint;

use Mergado\Service\AlertService;

class AdminAlertEndpoint extends AbstractEndpoint implements EndpointInterface
{
    public function disableSingle(): void
    {
        $alertName = $_POST['name'] ?? '';
        $feedName = $_POST['feed'] ?? '';

        if ($alertName !== '' && $feedName !== '') {
            $this->checkToken();

            $alertService = AlertService::getInstance();
            $alertService->setAlertDisabled($feedName, $alertName);
        }

        exit;
    }

    public function disableSection(): void
    {
        $sectionName = $_POST['section'] ?? '';

        if ($sectionName !== '') {
            $this->checkToken();

            $alertService = AlertService::getInstance();
            $alertService->setSectionDisabled($sectionName);
        }

        exit;
    }

    public function addSingle(): void
    {
        $alertName = $_POST['name'] ?? '';
        $feedName = $_POST['feed'] ?? '';
        if ($alertName !== '' && $feedName !== '') {
            $this->checkToken();

            $alertService = AlertService::getInstance();
            $alertService->setErrorActive($feedName, $alertName);
        }

        exit;
    }

    public function initEndpoints(): void
    {
        add_action('wp_ajax_ajax_disable_alert', [$this, 'disableSingle']);
        add_action('wp_ajax_ajax_disable_section', [$this, 'disableSection']);
        add_action('wp_ajax_ajax_add_alert', [$this, 'addSingle']);
    }
}
