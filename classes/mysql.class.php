<?php
  
  class mySQL {
 	private $conn;
 	
 	//Establishing connection to database
 	function __construct(){
 		require_once 'constants.php';
 		$this->conn = mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
		if (!$this->conn)
		  {
		  die('die-error: Could not connect to database: ' . mysql_error());
		  }
		 mysql_select_db(DB_NAME, $this->conn);
  	}
  	
  	
  	function createCrawler(){
		mysql_query("CREATE  TABLE `test`.`crawler` (`id` INT NOT NULL AUTO_INCREMENT,`url` VARCHAR(500) NOT NULL ,`visit` BINARY NOT NULL ,`ftch` BINARY NOT NULL ,`print` BINARY NOT NULL ,PRIMARY KEY (`id`) ,UNIQUE INDEX `url_UNIQUE` (`url` ASC) )");
		mysql_query("CREATE  TABLE `test`.`crawler_tree` (`id` INT NOT NULL AUTO_INCREMENT,`parent_id` INT NOT NULL ,`child_id` INT NOT NULL ,INDEX `parent` (`parent_id` ASC) , INDEX `child` (`child_id` ASC) ,PRIMARY KEY (`id`) , CONSTRAINT `parent` FOREIGN KEY (`parent_id` ) REFERENCES `test`.`crawler` (`id` ) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `child` FOREIGN KEY (`child_id` ) REFERENCES `test`.`crawler` (`id` ) ON DELETE NO ACTION ON UPDATE NO ACTION);");	
   }
	
	function dropCrawler(){
		mysql_query("DROP  TABLE `test`.`crawler_tree`");
		mysql_query("DROP  TABLE `test`.`crawler`");
	}
 	
  	
 	
	
	
	
		
	function crawlerPrint(){
			$result = mysql_query("SELECT * FROM crawler where print=0");

			while($row = mysql_fetch_array($result))
  			{
  					echo $row['id'] . " " . $row['url'];
  					echo "<br>";
  			}
  			mysql_query("UPDATE crawler SET print=1 WHERE print=0");
	}
	
	function selUnfetchedLink(){
			$result = mysql_query("SELECT * FROM crawler where visit=0");

			if($row = mysql_fetch_array($result)) 
			{
				$id = $row['id'];
				$_SESSION['fetchURL'] = $row['url'];
				$_SESSION['fetchUrlID'] = $id;
				mysql_query("UPDATE crawler SET visit=1 WHERE id='$id'");
				return true;
			}
  			else return false;
  		}
  		
  	function countLinks(){
  	$result = mysql_query("SELECT COUNT(*) as c FROM crawler where ftch=1");
	$row = mysql_fetch_array($result);
	echo "Number of links collected:".$row['c']."<br>";
	}

	
 	
 	function __destruct(){
 		mysql_close($this->conn);
 	}
 }
?>
