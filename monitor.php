<?php

# This script monitors a log file from fing app and updates the status of those devices
# on the server.
#
#	@author Mashhood Rastgar
#	@date 06/09/2014
date_default_timezone_set('UTC');

define('STATE_DOWN', 'down');
define('STATE_UP', 'up');

class Device {

	public $mac;
	public $lastUpdated;
	public $status;

	function __construct($lastUpdated, $status, $mac) {
		$this -> mac = $mac;
		$this -> lastUpdated = strtotime($lastUpdated);
		$this -> status = $status;
	}

}

class DeviceCollection {
	public  $devices = array();

	public function add($device) {
		$this -> devices[] = $device;
	}

	public function get($deviceMac) {
		foreach ($this -> devices as $device) {
			if($device -> mac == $deviceMac) {
				return $device;
			}
		}

		return null;
	}

	public function contains($device) {
		return ($this -> get($device -> mac) !== null);
	}

	public function containsExact($device) {
		$thisDevice = $this -> get($device -> mac);
		return 	($thisDevice !== null) &&
						($device -> mac == $thisDevice -> mac) &&
						($device -> status == $thisDevice -> status);
	}

	public function clear() {
		$this -> devices = array();
	}

	private function sortByDate($a, $b) {
		return intval($a -> lastUpdated) < intval($b -> lastUpdated);
	}

	public function filterLatest() {
		# removes all devices with same MAC
		# leaves the latest ones in
		usort($this -> devices, array($this, 'sortByDate'));

		$filtered = new DeviceCollection();
		foreach ($this -> devices as $device) {
			if(!$filtered -> contains($device)) {
				$filtered -> add($device);
			}
		}

		$this -> devices = $filtered -> devices;
		unset($filtered);
	}

	public function remove($device) {
		foreach ($this -> devices as $index => $thisDevice) {
			if($thisDevice -> mac === $device -> mac) {
				array_splice($this -> devices, $index, 1);
				break;
			}
		}
	}

	public function update($updatedDevice) {
		$device = $this -> get($updatedDevice -> mac);
		$device -> status = $updatedDevice -> status;
		$device -> lastUpdated = $updatedDevice -> lastUpdated;
	}

}

class Parser {
	public $fileName;
	public $collection;

	function __construct($fileName) {
		$this -> fileName = $fileName;
	}

	public function parse() {
		//2014/09/06 11:49:46;up;192.168.0.1;;;C8:D7:19:D7:85:FB;Cisco Consumer Products
		$logFile = file_get_contents($this -> fileName);
		$lines = explode("\n", $logFile);
		foreach ($lines as $deviceString) {
			$deviceArray = explode(';', $deviceString);
			if(count($deviceArray[5]) > 0) {
				$device = new Device(
					$deviceArray[0],
					$deviceArray[1],
					$deviceArray[5]
				);
				$this -> collection -> add($device);
			}
		}
		$this -> collection -> filterLatest();
	}
}

/* End of class definitions */

$devices = new DeviceCollection();
$updatedList = new DeviceCollection();
$parser = new Parser('network.csv');
$parser -> collection = $updatedList;

while(true) {
	echo "Parsing.. \n";
	$parser -> parse();
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
			# If it is not there, and state is DOWN, remove from updatedList
			if($updatedDevice -> status === STATE_DOWN) {
				$updatedList -> remove($updatedDevice);
			} else {
				$devices -> add($updatedDevice);
			}
		}
	}

	echo "Updating to server: \n";
	print_r($updatedList -> devices);

	if(count($updatedList -> devices) > 0) {
		# use the updatedList to update the server.
	}

	$updatedList -> clear();
	sleep(3000); # update every 5 mins
}


?>