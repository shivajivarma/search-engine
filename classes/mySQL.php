<?php
require_once 'constants.php';
 
 class mySQL {
 	private $conn;
 	
 	function __construct(){
 		$this->conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or
 				die('There was a problem connecting to he database.');
 	}
 	
 	function verify($un,$pwd){
 		$query="SELECT * 
 				FROM test.student
 				WHERE USERNAME = ? AND PASSWORD = ?
 				LIMIT 1";
 		if($stmt = $this->conn->prepare($query)) {
 			$stmt-> bind_param('ss',$un,$pwd);
 			$stmt->execute();
 			if($stmt->fetch()){ $stmt->close(); return true;}
 		}
 	}
 }
 ?>