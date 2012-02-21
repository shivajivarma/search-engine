<?php 
// Define the default, system-wide context. 
 include 'classes/proxy.php';
?>

<?php
 header("Content-type: text/plain");
  
  
  $original_file = file_get_contents("http://www.gitam.edu/",false,$content) or die("I could not load the website.");
  $stripped_file = strip_tags($original_file, "<a>");
  
  preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $stripped_file, $matches, PREG_SET_ORDER);

  //preg_match_all("|<div(?:[^>]*)>(.*)</div>|U", $stripped_file, $matches, PREG_SET_ORDER);
  
  //Adjusting links
  for($i=0; $i< sizeof($matches); $i++ ){
  	
  		if($matches[$i][1] == "#") {echo "deleting link:".$matches[$i][1]."\n";array_splice($matches,$i,1); $i--;}
  	
  		else if(strstr($matches[$i][1], "gitam.edu") == false) 
  		{
  			if(strstr($matches[$i][1], "gitam.edu") == false)
  			{
  				echo "deleting link: $i :".$matches[$i][1]."\n";array_splice($matches,$i,1);$i--;
  			}
  		}
  
		//	else if(strstr($matches[$i][1],"http") == false && strstr($matches[$i][1],"www") == false && strstr($matches[$i][1], $domain) == false) 
  		//	{echo "deleting link:".$matches[$i][1]."\n";array_splice($matches,$i,1);}
  }
  print_r($matches);
 ?>