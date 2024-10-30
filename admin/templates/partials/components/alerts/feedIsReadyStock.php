<?php

use Mergado\Service\AlertService;

$alertDefaultData = [
	'alertName' => 'feedIsReady',
	'alertSection' => $alertData['alertSection'],
	'type' => 'success',
	'text' => sprintf(__('<strong>The %s feed is ready</strong>. You can now go to the List of feeds and activate Availability feed in your Heureka account.', 'mergado-marketing-pack'), $wizardName),
	'closable' => false,
	'closableAll' => false,
];

$alertService = AlertService::getInstance();

if (!$alertService->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName']) && !$alertService->isSectionDisabled($alertData['alertSection'])) {
	include __DIR__ . '/template/alert.php';
}
?>
