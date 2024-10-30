<?php
$alertDefaultData = [
	'alertName' => 'longTime',
	'alertSection' => $alertData['alertSection'],
	'type' => 'warning',
	'text' => __('Creating XML feed takes a while based on how many products you have in the feed. Large feeds with thousands of items are divided into several generation steps. <strong>This process can take up to half an hour.</strong>', 'mergado-marketing-pack'),
	'closable' => false,
	'closableAll' => false,
];

include __DIR__ . '/template/alert.php';
?>