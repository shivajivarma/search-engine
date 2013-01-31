<?php
	session_start();
	if(isset($_GET['function']))
	{
		$crawler = new crawler();
		if($_GET['function'] == 'init') {$crawler->init();}
		else if($_GET['function'] == 'crawlerFetch') {$crawler->crawlerFetch();}
		else if($_GET['function'] == 'crawlerProcess') {$crawler->crawlerProcess();}
		
	}
?>
<?php
	class crawler{
	private $funcs;
	private $mysql;
	
		function __construct(){
			require_once "funcs.php";
			require_once "mysql.class.php";
			$this->funcs = new funcs();
			$this->mysql = new mySQL();
		}
		
		function init(){
					echo "Creating database<br>";
					$this->mysql->dropCrawler();
					$this->mysql->createCrawler();
					$url = $_SESSION['url'];
					mysql_query("INSERT INTO `test`.`crawler` (url,visit,ftch,print) VALUES ('$url',0,0,0)");
					$this->mysql->crawlerPrint();
		}
		
		function crawlerFetch()
		{
			require_once "constants.php";
			if(!$this->mysql->selUnfetchedLink()) {$this->mysql->countLinks();die('die-error: Completed');}
			echo "Fetching: ".$_SESSION['fetchURL']."<br>";
			
			$xml = $this->sxe("http://".MY_IP."/api/crawler.api.php?url=".$_SESSION['fetchURL']);
			if((string) $xml->fetch == 'failed') die("die-error: Unable to fetch content: $url<br>");
			$id = $_SESSION['fetchUrlID'];	
			mysql_query("UPDATE crawler SET ftch=1 WHERE id='$id'");
			$xml->asXML("indexData/".$id.".xml");
		}
		
		
		function sxe($url)
		{   
		    $xml = $this->funcs->fetch($url);
		    foreach ($http_response_header as $header)
		    {   
		        if (preg_match('#^Content-Type: text/xml; charset=(.*)#i', $header, $m))
		        {   
		            switch (strtolower($m[1]))
		            {   
		                case 'utf-8':
		                    // do nothing
		                    break;
		
		                case 'iso-8859-1':
		                    $xml = utf8_encode($xml);
		                    break;

		                default:
		                    $xml = iconv($m[1], 'utf-8', $xml);
		            }
		            break;
		        }
		    }

		    return simplexml_load_string($xml);
		}
		
		
		function crawlerProcess()
		{
			$parent_id  = $_SESSION['fetchUrlID'];
			
			$xml = simplexml_load_file("indexData/".$parent_id.".xml");
			$links = $xml->links[0];
			$count = (int) $links->attributes();
			$count = $count + (int) $xml->out->attributes();	
			echo "Total links in the page:".$count."<br>";
		
	
	
		
		
		$count=0;
		for($i=0; $i< count($links); $i++ ){
			$url=$links->link[$i];
	
			$r="SELECT * FROM crawler WHERE url='$url'";
		
			$result = mysql_query($r);
			$row = mysql_fetch_array($result);
			$child_id = $row['id'];

				
				if(!$row['url'])
				{		
						mysql_query("INSERT INTO `test`.`crawler` (url,visit,print,ftch) VALUES ('$url',0,0,0)");		
						$result = mysql_query("SELECT COUNT(*) as c FROM crawler");
						$row = mysql_fetch_array($result);
						$child_id = $row['c'];
						mysql_query("INSERT INTO `test`.`crawler_tree` (parent_id,child_id) VALUES ($parent_id,$child_id)");

						
						$count++;
				}
				else{
						mysql_query("INSERT INTO `test`.`crawler_tree` (parent_id,child_id) VALUES ('$parent_id','$child_id')");
				}	

		}
		echo "New links in the page:".$count."<br>";
		$this->mysql->crawlerPrint();
		}
	}		
?>