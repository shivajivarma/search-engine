<?php 
 
 $i=29;
 $j=1;
  //for($j=1;$j<=5;$j++)
 	//for($i=1; $i<=67 ; $i++)
 	//{
 	   		
 	   	$t = file_get_contents("http://127.0.0.1/photos/B".$j."_".$i.".html");
		
		$t = preg_replace("/[ ]+/i","_", $t);

		echo $t;
		$fp = fopen("photos/B".$j."_".$i."90(".$t.").html", 'w+') or die("I could not open $filename."); 
		fwrite($fp, "hi"); 
		fclose($fp);
	//}	
?>