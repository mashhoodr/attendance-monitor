<?php

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

			if(isset($deviceArray[5]) && count($deviceArray[5]) > 0) {
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


?>