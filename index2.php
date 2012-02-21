<?php
session_start();
require_once 'classes/user.php';
$user = new user();

	
	if($_POST){
	 	if(!empty($_POST['username']) && !empty($_POST['pwd'])){
			$response = $user->login($_POST['username'],$_POST['pwd']);
			echo "<div class='mesg'>".$response."</div>";
	 	}
	 	else echo "<div class='mesg'>Enter Both Username and Password</div>";
	}
	
	if(isset($_GET['status']) && $_GET['status'] == 'logout'){
		$user->logout();
		echo "<div class='mesg'>Logged Out Successfully</div>";
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>PROJECT</title>

	<link rel="stylesheet" href="css/default.css" type="text/css">
	<link rel="stylesheet" href="css/index.css" type="text/css">

	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$("#signIn").click(function(){
			$('#signDiv').css('visibility', 'visible');
  			$('#username').focus();
  			//$.get('hello.html', function(data){
  			  // $('#signDiv').html(data);
  			//});
	  	});
	  	
	  	$("#closeSignIn").click(function(){
			$("#signDiv").css("visibility", "hidden");
	  	});
	  	
	  	$(".mesg").click(function(){
			$(this).remove();
		});
	});
	</script>
</head>

<body>

	<div id="header">
		<!--img src="images/logo.png" alt="Project logo" title="logo"-->
		<input type="button" class="button" id="signIn" value="Sign in">
	</div>


	<div class="shadowBox" id="signDiv">
		<div id="closeSignIn"></div>
			<form name="signInForm" id="signInForm" method="post">
				<p>
				<label for="name">Username : <br> 
				<input class="input" type="text" id="username" name="username" >
				</label>						
				</p>
				
				<p>
				<label class="ss" for="pwd">Password :<br> 
				<input class="input" type="password" id="pwd" name="pwd">
				</label>
				</p>
				
				<p class="register">
				<a href="register.php">Register</a> | <a href="55">Lost Password?</a>
				<input type="submit"  id="submit"  class="button" value="Login">
				</p>
			</form>

	</div>
	
	<div id="container">
	</div>


	<div id='footer'>
		<div id='copyright'> Copyright &copy; 2011 Final Year Project</div>
	</div>
</body>

</html>