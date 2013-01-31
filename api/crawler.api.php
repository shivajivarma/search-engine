<?php
	header('Content-type: text/xml');
	$xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?"."><crawler/>");


	if(isset($_GET['url']))
	{
		$crawler = new crawler();
		if($crawler->fetchWebpage($_GET['url'])){  //Fetchs the webpage.
			$xml->fetch = "true";
			
			$xml->url = "http://".preg_replace('/^http[s]?:(\/)(\/)/', "",$_GET['url']);
			$domain = parse_url("http://".preg_replace('/^http[s]?:(\/)(\/)/', "",$_GET['url']));
			$xml->domain = $domain['host'];
			
			$xml->path = preg_replace('/(\/)[a-zA-z0-9\.]+$/', "",preg_replace('/(\/)$/', "",$domain['path']));
			$xml->path = preg_replace('/(\/([a-z0-9])+)(\/\.\.)$/i',"", $xml->path);
			$crawler->fetchLinks($xml); 		//Fetchs all the links.
			$xml->links->addAttribute('count',count($xml->links[0]));
			$xml->out->addAttribute('count',count($xml->out[0]));
			unset($xml->out->link);

			$crawler->indexableData($xml);		//Fetchs data for indexing.
			$xml->words->addAttribute('count',count($xml->words[0]));
			
		}
		else $xml->fetch = "failed";
	}
	
	print($xml->asXML());
?>
<?php
	class crawler{
	private $funcs;
	private $mysql;
	private $file;
	
		function __construct(){
			require_once "../classes/funcs.php";
			$this->funcs = new funcs();
		}

		//Fetchs the page
		function fetchWebpage($url)
		{
			if(!$this->file = $this->funcs->fetch("http://".preg_replace('/^http[s]?:(\/)(\/)(www.)?/', "$3",$_GET['url']))) return false;
			else return true;
		}
		
		function fetchLinks($xml){
			$links = $xml->addChild('links');
			$out = $xml->addChild('out');
			$stripped_file = strip_tags($this->file, "<a>");
  			preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $stripped_file, $matches, PREG_SET_ORDER);
  			for($i=0; $i< sizeof($matches); $i++){
  				$url = $matches[$i][1];
  				if(!($url == "#" || preg_match('/^mailto:/i', $url) || preg_match('/^javascript:/i', $url) || preg_match('/(\.(pdf)?(png)?(jpg)?(ppt)?(doc)?)$/i', $url) ))
  				{	
  					
					$url	= preg_replace('/^(\/)/', $xml->domain."/", $url);
					$url	= preg_replace('/^([a-zA-z0-9]*\/)/', $xml->domain.$xml->path.'/$0',$url);
					$url	= preg_replace('/^(\.\/)(\.\.\/)(\.\.\/)/', $xml->domain.preg_replace('/(\/)[a-z0-9]+$/i', "",preg_replace('/(\/)[a-z0-9]+$/i', "",$xml->path))."/",$url);
					$url	= preg_replace('/^(\.\/)(\.\.\/)/', $xml->domain.preg_replace('/(\/)[a-z0-9]+$/i', "",$xml->path)."/",$url);
					$url	= preg_replace('/^(\.\/)/', $xml->domain.$xml->path."/",$url);
					$url	= preg_replace('/^(\.\.\/)(\.\.\/)/', $xml->domain.preg_replace('/(\/)[a-z0-9]+$/i', "",preg_replace('/(\/)[a-z0-9]+$/i', "",$xml->path))."/",$url);
					$url	= preg_replace('/^(\.\.\/)/', $xml->domain.preg_replace('/(\/)[a-z0-9]+$/i', "",$xml->path)."/",$url);
					
					
					if(strstr($url, (string) $xml->domain) == true)
					 {
					 	if(!$xml->XPath("/crawler/links/link[. = '$url']"))
						$links->addChild('link',$url);	
					}	
					else if(!$xml->XPath("/crawler/out/link[. = '$url']"))
						$out->addChild('link',$url);			
				}
			}
			
			for($i=0; $i< sizeof($xml->links[0]); $i++ ){
				$xml->links[0]->link[$i] = preg_replace('/^(http[s]?:(\/)(\/))?(www.)?/i',"$4",$xml->links[0]->link[$i]);
			}	
		}
		
		
		function indexableData($xml){
			preg_match("/<title>(.*)<\/title>/siU", $this->file, $title);
			$xml->title = $title[1];
			$xmlWords = $xml->addChild('words');
			
			$this->file = $this->remove_script($this->file);  //Removes scripts from the webpage.
			$this->file = $this->remove_style($this->file);  //Removes style from the webpage.
			
			
			$this->file = strip_tags($this->file);  //Trims all tags.
			$tokens = $this->splitIt($this->file);	//Spilts the words in the page.
			$words = array_count_values($tokens);
			
			foreach($words as $word => $count)
			{
				$word = urlencode($word);
				$word = $xmlWords->addChild('word',$word);
				$word->addAttribute('count',$count);
			}
			
		}
		
		function remove_script($file) { //Removes scripts from the webpage
			$do = true;
    		while ($do) {
      		  $start = stripos($file,'<script');
      		  $stop = stripos($file,'</script>');
       			if ((is_numeric($start))&&(is_numeric($stop))) {
        	    	$file = substr($file,0,$start).substr($file,($stop+strlen('</script>')));
	    	   } 
	    	    else {
	    	        $do = false;
	    	    }
	    	}
	    	return trim($file);
		}
		
		
		function remove_style($file) { //Removes scripts from the webpage
			$do = true;
    		while ($do) {
      		  $start = stripos($file,'<style');
      		  $stop = stripos($file,'</style>');
       			if ((is_numeric($start))&&(is_numeric($stop))) {
        	    	$file = substr($file,0,$start).substr($file,($stop+strlen('</style>')));
	    	   } 
	    	    else {
	    	        $do = false;
	    	    }
	    	}
	    	return trim($file);
		}

		
		function splitIt($file){ //Spilts the words in the page
		
			$stopWords = unserialize(file_get_contents("../data/datastop words.dat"));
			$file = str_replace($stopWords," ",$file);
			
			$file = preg_replace('/(\s+)\/\*([^\/]*)\*\/(\s+)/s', " ", $file);
			
			$stopChars = array('&nbsp;','&copy;','&amp;','(',')','|',',',':','"','&','/','Ã','©','“','”','»');
			$file = str_replace($stopChars," ", $file);
			
			$file = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $file);
			$file = preg_replace("/( )+/", " ", $file);
			$file = preg_replace("/( )+$/", "", $file);
			$file = preg_replace("/^( )+/", "", $file);
			return explode(" ",strtolower($file));
			
	}

		
}	
?>