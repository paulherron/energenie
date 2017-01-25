<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$switch = $_GET['switch'];
if (!($switch == 1 || $switch == 2)) {
	die("Invalid switch. Should be '1' or '2'.");
}

if (!isset($_GET['action'])) {
	echo file_get_contents('switch-'.$switch.'-status.json');
	die;
}

$action = $_GET['action'];
if (!($action == 'on' || $action == 'off')) {
	die("Invalid action. Should be 'on' or 'off'.");
}

// Always start by turning the switch off first. If the requested action is 'off', there'll be
// nothing further to do. If the requested action is 'on', the device will be started from a
// freshly powered-on state - useful for something like an electric blanket that might need to
// be turned off and on for the heat to trigger.
//
// Send the same command several times, as depending on interference it can sometimes be missed.
$command = 'for i in $(seq 3); do python ../control.py '.$switch.' off 2>&1; done;';

if ($action == 'on') {
	$command .= ' for i in $(seq 3); do python ../control.py '.$switch.' on 2>&1; done';
}

exec($command, $output, $exitCode);

$response = array(
	'error' => (bool) $exitCode,
	'lastCommand' => $action,
	'switch' => $switch,
	'output' => $output
);

echo json_encode($response);
