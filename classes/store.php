<?php
	$s = file_get_contents("a.txt");
	
	
	$s = explode("\n",$s);
	
	
	
	
	
	
	$fp = fopen("stop char.dat", 'w+') or die("I could not open $filename."); 
	fwrite($fp, serialize($s)); 
	fclose($fp); 
 
	$s = unserialize(file_get_contents("stop char.dat"));
	print_r($s);
?>