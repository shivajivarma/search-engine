<?php

class CrawlerRepository
{

    private $mysqli;


    function __construct()
    {
        $this->mysqli = Database::getInstance()->getConnection();
    }


    function createCrawlerSchema()
    {
        mysqli_query($this->mysqli, "CREATE  TABLE `crawler` (`id` INT NOT NULL AUTO_INCREMENT,`url` VARCHAR(500) NOT NULL ,`visit` BINARY NOT NULL ,`ftch` BINARY NOT NULL ,`print` BINARY NOT NULL ,PRIMARY KEY (`id`) ,UNIQUE INDEX `url_UNIQUE` (`url` ASC) )");
        mysqli_query($this->mysqli, "CREATE  TABLE `crawler_tree` (`id` INT NOT NULL AUTO_INCREMENT,`parent_id` INT NOT NULL ,`child_id` INT NOT NULL ,INDEX `parent` (`parent_id` ASC) , INDEX `child` (`child_id` ASC) ,PRIMARY KEY (`id`) , CONSTRAINT `parent` FOREIGN KEY (`parent_id` ) REFERENCES `test`.`crawler` (`id` ) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `child` FOREIGN KEY (`child_id` ) REFERENCES `test`.`crawler` (`id` ) ON DELETE NO ACTION ON UPDATE NO ACTION);");
    }


    function dropCrawlerSchema()
    {
        mysqli_query($this->mysqli, "DROP  TABLE `crawler_tree`");
        mysqli_query($this->mysqli, "DROP  TABLE `crawler`");
    }

    function saveCrawler($url)
    {
        mysqli_query($this->mysqli, "INSERT INTO `crawler` (url,visit,ftch,print) VALUES ('$url',0,0,0)");
    }

    function fetchAllCrawlerForPrint($print)
    {
        return mysqli_query($this->mysqli, "SELECT * FROM crawler where print=" . $print);
    }


    function updateAllCrawlerToPrinted()
    {
        mysqli_query($this->mysqli, "UPDATE crawler SET print=1 WHERE print=0");
    }

    function fetchUnvisitedLink()
    {
        $result = mysqli_query($this->mysqli, "SELECT * FROM crawler where visit=0");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }
    }

    function updateLinkAsVisited($id)
    {
        mysqli_query($this->mysqli, "UPDATE crawler SET visit=1 WHERE id='$id'");
    }

    function updateLinkAsFetched($id)
    {
        mysqli_query($this->mysqli, "UPDATE crawler SET ftch=1 WHERE id='$id'");
    }

    function fetchByUrl($url)
    {
        $result = mysqli_query($this->mysqli, "SELECT * FROM crawler WHERE url='$url'");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }

    }


    function fetchCount()
    {
        $result = mysqli_query($this->mysqli, "SELECT COUNT(*) as c FROM crawler");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }

    }


    function saveCrawlerTree($parent_id, $child_id){
        mysqli_query($this->mysqli, "INSERT INTO `crawler_tree` (parent_id,child_id) VALUES ('$parent_id','$child_id')");
    }

    function countForFetchedLinks(){
        $result = mysqli_query($this->mysqli, "SELECT COUNT(*) as c FROM crawler where ftch=1");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }
    }

    function getFetchedLink(){
        $result = mysqli_query($this->mysqli, "SELECT * FROM crawler where ftch=1");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }
    }

}

?>
