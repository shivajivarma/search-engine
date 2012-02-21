<?php

require 'mySQL.php';
 
 class user{
 	
 	function login($un,$pwd){
 		$mysql = new mySQL();
 		$ensure_credentials = $mysql->verify($un,md5($pwd));
 		
 		if($ensure_credentials){
 			$_SESSION['status']='authorized';
 			header("location: student.php");
 		}
 		else return "Please enter a correct username and password";
 	}
 	
 	function logout(){
 		if(isset($_SESSION['status'])) unset($_SESSION['status']);
 		
 		if(isset($_COOKIE[session_name()]))
 				setcookie(session_name(), '', time() - 1000);
				session_destroy();
	} 	
	

	function confirm_Member(){
		session_start();
		if(isset($_SESSION['status']) && $_SESSION['status'] == 'authorized');
		else header("location: index.php");	
	}		
 }
 ?>