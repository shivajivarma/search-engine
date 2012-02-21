$fp = fopen("data/temp.dat", 'w+') or die("I could not open $filename."); 
	fwrite($fp, serialize($matches)); 
	fclose($fp); 
 
	$s = unserialize(file_get_contents("data/temp.dat"));