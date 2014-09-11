<?php

# This script monitors a log file from fing app and updates the status of those devices
# on the server.
#
#	@author Mashhood Rastgar
#	@date 06/09/2014

require_once 'Device.class.php';
require_once 'DeviceCollection.class.php';
require_once 'Parser.class.php';


date_default_timezone_set('UTC');

define('STATE_DOWN', 'down');
define('STATE_UP', 'up');
define('OUTPUT_FILE', 'network.csv');

$devices = new DeviceCollection();
$updatedList = new DeviceCollection();
$parser = new Parser(OUTPUT_FILE);
$parser -> collection = $updatedList;

# create a temp file for locking and stopping
$tempFile = substr(md5(date("mm/dd/yy h:i:s")), 0, 8);

$fp = fopen($tempFile,"wb");
fwrite($fp,"");
fclose($fp);

while(file_exists($tempFile)) {
	echo "Running the scanner...\n";
	shell_exec('fing -r 1 -o log,csv,' . OUTPUT_FILE);
	echo "Parsing.. \n";
	$parser -> parse();

	foreach($devices -> devices as $previousDevice) {
		if(!$updatedList -> contains($previousDevice)) {
			echo "Device down: " . $previousDevice -> mac . "\n";
			$previousDevice -> status = STATE_DOWN;
			$previousDevice -> lastUpdated = strtotime('now');
			$updatedList -> add($previousDevice);
			$devices -> remove($previousDevice);
		}
	}

	foreach ($updatedList -> devices as $updatedDevice) {
		# If updatedDevice is in the list [as is], remove it from updatedList - update its time on devices
		if($devices -> contains($updatedDevice)) {
			if($devices -> containsExact($updatedDevice)) {
				$updatedList -> remove($updatedDevice);
			}

			# If it has changed, leave it in updatedList, update it in devices
			$devices -> update($updatedDevice);
		} else {
			# If it is not there, and state is UP, leave it in updatedList and add it to devices
			if($updatedDevice -> status === STATE_UP) {
				$devices -> add($updatedDevice);
			}
		}
	}

	echo "Updating to server: \n";
	print_r($updatedList -> devices);

	// if(count($updatedList -> devices) > 0) {
	// 	# use the updatedList to update the server.
	// 	foreach ($updatedList -> devices as $device) {
	// 		// send the data to the server
	// 		$url = sprintf("http://dev.marketlytics.com/attend/store.php?mac=%s&lastUpdated=%s&status=%s",
	// 	    $device -> mac,
	// 			$device -> status,
	// 			$device -> lastUpdated
	// 		);

	// 		$ch = curl_init($url);
	// 		curl_exec($ch);
	// 		curl_close($ch);
	// 	}
	// }

	unlink(OUTPUT_FILE);
	$updatedList -> clear();
	echo "\n\n\n\n\n\n\n";
	sleep(10); # update every 1 min
}

echo "Monitor terminated!";


?>