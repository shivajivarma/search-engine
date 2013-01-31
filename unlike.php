<?php


	if(isset($_GET['domainID']) && isset($_GET['cat']))
	{
		$cat = $_GET['cat'];
		
				$domainXML = simplexml_load_file("./data/links/".(int) $_GET['domainID'].".xml");
				if($catID = $domainXML->XPath("/domain/$cat"))
				{
					$att = 'unlike';
					if(!$unlike = $catID[0]->attributes()->$att) $catID[0]->addAttribute('unlike',1);
					else if((int) $unlike <= 2)	 $catID[0]->attributes()->$att = (int) $catID[0]->attributes()->$att + 1;
					else unset($domainXML->$cat);
				}
				$domainXML->asXML("./data/links/".(int) $_GET['domainID'].".xml");
			echo "<h1>Thanks for helping us. Link as been reported.</h1>";
	}
				
				
?>