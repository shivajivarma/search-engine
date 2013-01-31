<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>Crawler</title>

	<link rel="stylesheet" href="css/default.css" type="text/css">
	<link rel="stylesheet" href="css/crawler.css" type="text/css">

	
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript">
			 
	function crawler(){
			$('#status').html(' Fetching: ');
			$('#preloader').html('<img title="preloader" src="images/preloader.gif">');
			$('#fetch').prepend("<hr>");

						$.get("./classes/crawler.class.php",{ function: 'crawlerFetch' },
  	 						function(data){
  	 						
  	 							if(data.match('die-error: Completed')) {
  	 								$('#fetch').prepend(data.replace('die-error: ',''));
  	 								$('#status').html(' Crawling completed: ');
  	 								$('#preloader').html('<img title="preloader"  src="images/check.png">');
   	 								return false;
								}
								else if(data.match('die-error: Unable to fetch content')) {
  	 								$('#fetch').prepend(data.replace('die-error: ',''));
  	 								if(!crawler()) return false;
  	 							}
  	 							
  	 								$('#fetch').prepend(data);
  	 							
								
									$('#status').html(' Processing: ');
									$.get("./classes/crawler.class.php",{ function: 'crawlerProcess'},
  	 								function(data){
  		 	   							$('#fetch').prepend(data);
  		 	   							$('#preloader').html('<img title="preloader"  src="images/check.png">');
  		 	   							
  		 	   							if(!crawler()) return false;		  		 	   			 		 		
  		 	   						});								
  	 					
  	   		 	   			});
  		 	   		return false;
	}
	
	
	
		$(document).ready(function(){
				
				if(document.getElementsByName("url")[0].value != ""){
				$('#status').html(' Fetching: ');
				$('#preloader').html('<img title="preloader" src="images/preloader.gif">');

				
				$.get("./classes/crawler.class.php",{ function: 'init'},
  	 				function(data_1){
  	 				if(data_1.match('die-error')) {
  	 					$('#fetch').prepend(data_1.replace('die-error: ',''));
  	 					$('#preloader').html('<img title="preloader"  src="images/close-active.png">');
  	 					return false;
					}
   		 	  		$('#fetch').prepend(data_1);

   		 	  		if(!crawler()) return; //Recursive function to crawl the website   		 	   	
   		 	   	});
   		 	   	
    			}
    			
		});
	</script>
	
</head>

<body>
	<div id="header">
		<span id="title" onclick="location. href='./crawler.php'">Crawler</span>
		<div id="back-button" onclick="location. href='./'">Back</div>	
	</div>
	
	<div id='crawl-form-div'>
		<div id='crawl-form'>
		 <form method="get">
			<table><tr>
						<td> Enter URL : http://www.</td>
						<td> <input id="input" type="text" value="<?php if(isset($_GET['url'])) { session_start(); $_SESSION['url'] = $_GET['url'];echo $_GET['url']; } ?>" name="url"> </td>
						<td><input class="submit" value="Submit" type="submit"></td>
						<td id="status"></td>
						<td id="preloader"></td>
					</tr>
			</table> 
		</form>
	</div>	
	</div>
	
	<div id="container">
	 <div id="domain-info">
	 
	 </div>
	 <div id="fetch">
	 
	 
	 </div>
	 
	 
	</div>

</body>

</html>