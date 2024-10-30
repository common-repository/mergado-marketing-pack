<?php

use Mergado\Service\AlertService;

$alertDefaultData = [
	'alertName' => AlertService::ALERT_NAMES['NO_FEED_UPDATE'],
	'alertSection' => $alertData['alertSection'],
	'type' => 'danger',
	'text' => sprintf(__('Check your cron settings. Feed was last updated %s <br><small>WP cron settings or cron URL for external service can be found in the detail of your feed.</small>', 'mergado-marketing-pack'), $feedBoxData['lastUpdate']),
	'closable' => false,
	'closableAll' => false,
];

$alertService = AlertService::getInstance();
if (!$alertService->isAlertDisabled($alertData['feedName'], $alertDefaultData['alertName'])) {
	include __DIR__ . '/template/alert.php';
}
?>
