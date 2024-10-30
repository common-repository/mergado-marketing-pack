<?php

use Mergado\Service\AlertService;

$alertDefaultData = [
	'alertName' => AlertService::ALERT_NAMES['ERROR_DURING_GENERATION'],
	'alertSection' => $alertData['alertSection'],
	'type' => 'danger',
	'text' => __('The last generation of feed failed.<br>Please start the generation again with the FINISH MANUALLY button.', 'mergado-marketing-pack'),
	'closable' => true,
	'closableAll' => false,
];

$alertService = AlertService::getInstance();
if (!$alertService->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName'])) {
	include __DIR__ . '/template/alert.php';
}
?>
