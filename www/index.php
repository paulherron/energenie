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

$command = 'python ../control.py '.$action.' 2>&1';
exec($command, $output, $returnVar);

$response = array(
	'error' => (bool) $returnVar,
	'action' => $action,
	'output' => $output
);

echo json_encode($response);
