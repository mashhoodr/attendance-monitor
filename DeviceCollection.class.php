<?php



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

?>