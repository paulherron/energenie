<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['action'])) {
	echo file_get_contents('index.json');
	die;
}

$action = $_GET['action'];
if (!($action == 'on' || $action == 'off')) {
	die("Invalid action. Should be 'on' or 'off'.");
}

$switch = $_GET['switch'];
if (!($switch == 1 || $switch == 2)) {
	die("Invalid switch. Should be '1' or '2'.");
}

// Send the same command several times, as depending on interference it can sometimes be missed.
$command = 'for i in $(seq 3); do python ../control.py '.$switch.' '.$action.' 2>&1; done';
exec($command, $output, $exitCode);

$response = array(
	'error' => (bool) $exitCode,
	'action' => $action,
	'switch' => $switch,
	'output' => $output
);

echo json_encode($response);
