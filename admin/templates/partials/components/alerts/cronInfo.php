<?php
$alertDefaultData = [
	'alertName' => 'cronInfo',
	'alertSection' => $alertData['alertSection'],
	'type' => 'warning',
	'text' => __('Cron is used to periodically call the selected script, in our case to <strong>start feed regeneration and keep it up to date.</strong>', 'mergado-marketing-pack'),
	'closable' => false,
	'closableAll' => false,
];

include __DIR__ . '/template/alert.php';
?>