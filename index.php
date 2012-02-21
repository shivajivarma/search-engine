
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>PROJECT</title>

	<link rel="stylesheet" href="css/default.css" type="text/css">
	
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		
		
		$('#mesg').toggle();
		$("#settings").hover(function(){
			$('#mesg').toggle();
	    });
	    
	
		$('#suggestions').html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img title="preloader" src="images/preloader.gif">');		
		$.get("classes/suggestions.php", { query: document.getElementsByName("query")[0].value },
  	 		function(data){
  		 	   $('#suggestions').html(data);   		
  		 });
  		 	    
	});
	</script>
	
</head>

<body>
	<div id="header">
		
		<span id="title" onclick="location. href='./'">Project</span>
		
		<form method="get">
			<table><tr>
						<td><input id="input" type="text" value="<?php echo $_GET['query']; ?>" name="query"></td>
						<td><input id="submit" value="Search" type="submit"></td>
					</tr>
			</table> 
		</form>
		
		<a href="crawler.php"><div class="button" id="settings"></div></a>
		<div id="mesg">Crawler</div>
	</div>
	
	<div id='suggestions'></div>	
	
	

	
	<div id="container">
		
	</div>


	<div id='footer'>
		<div id='copyright'> Copyright &copy; 2011 Final Year Project</div>
	</div>
</body>

</html>