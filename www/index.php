<?php
if (!isset($_GET['action'])) {
	die('No action specified');
}

$action = $_GET['action'];

if (!($action == 'on' || $action == 'off')) {
	die("Invalid action. Should be 'on' or 'off'.");
}

$command = 'python /Users/paulherron/Projects/energenie/app/control.py '.$action.' 2>&1';
exec($command, $output, $returnVar);

$response = array(
	'error' => (bool) $returnVar,
	'output' => $output
);

header('Content-Type: application/json');
echo json_encode($response);
