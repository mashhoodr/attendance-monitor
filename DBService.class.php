<?php

define('DATABASE','marketly_witend');
define('USER_NAME','marketly_witend');
define('PASSWORD','$}mpFZfv(wDo');
define('HOST','localhost');

class DBService {

	private $con;

	public function DBService() {
		$this -> con = mysqli_connect(HOST,USER_NAME,PASSWORD,DATABASE);
		if(mysqli_connect_errno()) {
		  	echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  	exit();
		}
	}

	public function query($query) {

		$result = mysqli_query($this->con,$query);
		if(!$result) {
			echo $query . ' :: Query failed due to <br />';
			echo mysqli_error($this -> con) . '<--';
			exit();
		}
		return $result;
	}

	public function getResult($query) {

		if($row = $this -> query($query)) {
			return mysqli_fetch_array($row, MYSQL_ASSOC);
		}
		return null;
	}

	public function getResults($query) {
		$recordsArray = array();
		if($rows = $this -> query($query)) {
			while($records = mysqli_fetch_array($rows, MYSQL_ASSOC)) {
				$recordsArray[] = $records;
			}
			return $recordsArray;
		}
		return null;
	}

}

?>