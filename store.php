<?php

# stores the data on the server
require_once 'DBService.class.php';

$mac = $_GET['mac'];
$status = $_GET['status'];
$lastUpdated = $_GET['lastUpdated'];

if(isset($mac) && isset($status) && isset($lastUpdated) &&
		$mac !== '' && $status !== '' && $lastUpdated !== '') {

	$query = sprintf("INSERT into devices(mac, status, lastUpdated) VALUES ('%s','%s',%s)",
		$mac,
		$status,
		$lastUpdated
	);

	$dbService = new DBService();
	$dbService -> query($query);
}

echo "It is done; " . $mac . "\n";

?>