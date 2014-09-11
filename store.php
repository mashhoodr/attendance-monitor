<?php

# stores the data on the server

define('DATABASE','marketly_witend');
define('USER_NAME','marketly_witend');
define('PASSWORD','$}mpFZfv(wDo');
define('HOST','localhost');

# database
$con = mysqli_connect(HOST,USER_NAME,PASSWORD,DATABASE);
if(mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	exit();
}

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

	mysqli_query($con,$query);
}

echo "This is a sample page.";

?>