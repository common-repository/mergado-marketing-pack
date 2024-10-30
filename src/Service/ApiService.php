<?php declare(strict_types=1);

namespace Mergado\Service;

use Mergado\Endpoint\AdminAlertEndpoint;
use Mergado\Endpoint\AdminCookieEndpoint;
use Mergado\Endpoint\AdminCronSettingsEndpoint;
use Mergado\Endpoint\AdminFeedGenerationEndpoint;
use Mergado\Endpoint\AdminNewsEndpoint;
use Mergado\Endpoint\AdminScheduleEstimateEndpoint;
use Mergado\Endpoint\AdminWizardEndpoint;
use Mergado\Endpoint\PublicGa4Endpoint;
use Mergado\Traits\SingletonTrait;

class ApiService {

    use SingletonTrait;

    /**
     * @var AdminAlertEndpoint
     */
    private $adminAlertEnpoint;

    /**
     * @var AdminCookieEndpoint
     */
    private $adminCookieEndpoint;

    /**
     * @var AdminCronSettingsEndpoint
     */
    private $adminCronSettingsEndpoint;

    /**
     * @var AdminFeedGenerationEndpoint
     */
    private $adminFeedGenerationEndpoint;

    /**
     * @var PublicGa4Endpoint
     */
    private $publicGa4Endpoint;

    /**
     * @var AdminNewsEndpoint
     */
    private $adminNewsEndpoint;

    /**
     * @var AdminScheduleEstimateEndpoint
     */
    private $adminScheduleEstimateEndpoint;
    /**
     * @var AdminWizardEndpoint
     */
    private $adminWizardEndpoint;

    public function __construct()
    {
        $this->adminAlertEnpoint = new AdminAlertEndpoint();
        $this->adminCookieEndpoint = new AdminCookieEndpoint();
        $this->adminCronSettingsEndpoint = new AdminCronSettingsEndpoint();
        $this->adminFeedGenerationEndpoint = new AdminFeedGenerationEndpoint();
        $this->publicGa4Endpoint = new PublicGa4Endpoint();
        $this->adminNewsEndpoint = new AdminNewsEndpoint();
        $this->adminScheduleEstimateEndpoint = new AdminScheduleEstimateEndpoint();
        $this->adminWizardEndpoint = new AdminWizardEndpoint();
    }

    public function initEndpoints() : void
    {
        $this->initAdminEndpoints();
        $this->initPublicEndpoints();
    }

    public function initAdminEndpoints(): void
    {
        if(is_user_logged_in() && current_user_can('manage_options')) {
            $this->adminAlertEnpoint->initEndpoints();
            $this->adminCookieEndpoint->initEndpoints();
            $this->adminCronSettingsEndpoint->initEndpoints();
            $this->adminFeedGenerationEndpoint->initEndpoints();
            $this->adminNewsEndpoint->initEndpoints();
            $this->adminScheduleEstimateEndpoint->initEndpoints();
            $this->adminWizardEndpoint->initEndpoints();
        }
    }

    public function initPublicEndpoints(): void
    {
        $this->publicGa4Endpoint->initEndpoints();
    }
}
