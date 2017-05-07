<?php



class funcs{
 	function __construct(){
 		require_once 'constants.php';
 		
 		if(PROXY){
 			// Define the default, system-wide context. 
			$r_default_context = stream_context_get_default ( 
			    array ( 
	    	    'http' => array (
	    	        	'proxy' => PROXY_IP.":".PROXY_PORT, 
	    	        	'request_fulluri' => True,
	    	        	'timeout' => 8 
	    	        	), 
	    	    ) 
	    	);
			// Though we said system wide, some extensions need a little coaxing. 
			libxml_set_streams_context($r_default_context);
		}
		else{
			// Define the default, system-wide context. 
			$r_default_context = stream_context_get_default ( 
			    array ( 
	    	    'http' => array (
	   	    	        	'timeout' => 8 
	    	        	), 
	    	    ) 
	    	);
			// Though we said system wide, some extensions need a little coaxing. 
			libxml_set_streams_context($r_default_context);
		}
 	}
 	
 	function fetch($url){
  		$var = file_get_contents("$url",0, stream_context_create()) or false;
 		return $var;
 	}
 	
 	function store($file,$str){
 		$fp = fopen($file, 'w+') or die("Could not open the file.");
 		fwrite($fp,$str); 
		fclose($fp);
 	}
 	
 	function isValidURL($url)
	{
		return preg_match("#^http://www\.[a-z0-9-_.]+\.[a-z]{2,4}(/[a-z0-9-_.]+)*/?$#i",$url);
	}
 }
?>