<?php

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

?>