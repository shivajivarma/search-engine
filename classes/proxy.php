<?php

 
	if( isset($_GET['proxy']) && isset($_GET['proxy_ip']) && isset($_GET['proxy_port'])){	
		$source='constants.php';
		$target='out.txt';
 		$file=fopen($source, 'r') or exit("Unable to open file!");
 		$th=fopen($target, 'w');
 		//fwrite($th, "<?php \n //define constants here");
 		
 		while(!feof($file))
    	{
   			$str = fgets($file);
   			if (strpos($str, "'PROXY'")!==false) {
        		$str = "define('PROXY',".$_GET['proxy'].");\n";
    		}
    		elseif (strpos($str, "'PROXY_IP'")!==false) {
				$str = "define('PROXY_IP','".$_GET['proxy_ip']."');\n";
    		}
    		elseif (strpos($str, "'PROXY_PORT'")!==false) {
				$str = "define('PROXY_PORT','".$_GET['proxy_port']."');\n";
    		}
    		
    		fwrite($th, $str);

  		}
  		fclose($file);
		fclose($th);

		// delete old source file
		if(unlink($source));
		// rename target file to source file
		rename($target, $source);

	 	echo "<b>Submitted</b>";
	}
	
 require_once 'constants.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<title>Proxy settings</title>
	<link rel="stylesheet" href="../assets/css/default.css" type="text/css">
</head>
<body>
<div id="back-button" onclick="location. href='../'">Back</div>	

<form action="" method="get">
	<table><tr><td>Use proxy :</td>
				<td><input type="radio" name="proxy" value="true" <?php if(PROXY) echo 'checked="checked"';?>>True 
				<input type="radio" name="proxy" value="false" <?php if(PROXY == false) echo 'checked="checked"';?>>False</td>
			</tr>
			
		<tr><td>PROXY_IP :</td>
			<td><input type="text" name="proxy_ip" value="<?php echo PROXY_IP;?>"></td>
			</tr>
			
		<tr><td>PROXY_PORT :</td>
			<td><input type="text" name="proxy_port" value="<?php echo PROXY_PORT;?>"></td>
			</tr>
			
		<tr><td><input class="submit" type="submit"></td></tr>
	</table>
	
</form> 
</body>
</html>