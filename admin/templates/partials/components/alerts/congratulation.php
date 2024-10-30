<?php

use Mergado\Service\AlertService;

$alertDefaultData = [
	'alertName' => 'congratulation',
	'alertSection' => $alertData['alertSection'],
	'type' => 'success',
	'text' => sprintf( __('<strong>Congratulations!</strong> You have just created your first %s feed in Mergado Pack. Now you can <strong>create an export in the Mergado App</strong> by pressing the <strong>button "CREATE EXPORT IN MERGADO"</strong>', 'mergado-marketing-pack'), $feedBoxData['feedName']),
	'closable' => true,
	'closableAll' => true,
];

$alertService = AlertService::getInstance();
if (!$alertService->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName']) && !$alertService->isSectionDisabled($alertData['alertSection'])) {
	include __DIR__ . '/template/alert.php';
}
?>
