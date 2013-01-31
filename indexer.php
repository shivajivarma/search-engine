<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<title>Indexer</title>

	<link rel="stylesheet" href="css/default.css" type="text/css">
	<link rel="stylesheet" href="css/crawler.css" type="text/css">

	
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript">
			 
		
		function indexIt(){
		
			$.get("./classes/indexer.class.php",{ function: 'indexIt'},
  	 				function(data_1){
  	 				if(data_1.match('die-error')) {
  	 					$('#fetch').prepend(data_1.replace('die-error: ',''));
  	 					$('#preloader').html('<img title="preloader"  src="images/check.png">');
  	 					return false;
					}
   		 	  		$('#fetch').prepend(data_1);

   		 	  		if(!indexIt()) return false; //Recursive function to crawl the website   		 	   	
   		 	   	});
		
		}
	
	
		$(document).ready(function(){
				
				$('#start-button').click(function(){
				
					$('#status').html(' Indexing: ');
					$('#preloader').html('<img title="preloader" src="images/preloader.gif">');
					
					$.get("./classes/indexer.class.php",{ function: 'init'},
  	 				function(data){
  	 	  						if(!indexIt()) return;
						
  	 	  			});

    			});
    			
		});
	</script>
	
</head>

<body>
	<div id="header">
		<span id="title" onclick="location. href='./crawler.php'">Indexer</span>
		<div id="back-button" onclick="location. href='./'">Back</div>
		<div id="start-button">Start Indexing</div>	

	</div>
	
	<div id='crawl-form-div'>
		<div id='crawl-form'>
		 <form method="get">
			<table><tr>
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