<?php

include_once(dirname(__DIR__) . '\class.database.php');

class CrawlerRepository
{

    private $mysqli;


    function __construct()
    {
        $this->mysqli = Database::getInstance()->getConnection();
    }


    function createCrawlerSchema()
    {
        mysqli_query($this->mysqli, "CREATE  TABLE `crawler` (`id` INT NOT NULL AUTO_INCREMENT,`url` VARCHAR(500) NOT NULL ,`status` VARCHAR(500) , PRIMARY KEY (`id`) ,UNIQUE INDEX `url_UNIQUE` (`url` ASC) )");
        mysqli_query($this->mysqli, "CREATE  TABLE `crawler_tree` (`id` INT NOT NULL AUTO_INCREMENT,`parent_id` INT NOT NULL ,`child_id` INT NOT NULL ,INDEX `parent` (`parent_id` ASC) , INDEX `child` (`child_id` ASC) ,PRIMARY KEY (`id`) , CONSTRAINT `parent` FOREIGN KEY (`parent_id` ) REFERENCES `crawler` (`id` ) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `child` FOREIGN KEY (`child_id` ) REFERENCES `crawler` (`id` ) ON DELETE NO ACTION ON UPDATE NO ACTION);");
    }


    function dropCrawlerSchema()
    {
        mysqli_query($this->mysqli, "DROP  TABLE `crawler_tree`");
        mysqli_query($this->mysqli, "DROP  TABLE `crawler`");
    }

    function saveCrawler($url)
    {
        $url = urldecode($url);
        mysqli_query($this->mysqli, "INSERT INTO `crawler` (url, status) VALUES ('$url', 'OPEN')");
        return mysqli_insert_id($this->mysqli);
    }


    function updateLinkStatus($id, $status)
    {
        mysqli_query($this->mysqli, "UPDATE crawler SET status='$status' WHERE id='$id'");
    }


    function fetchOpenLink()
    {
        $result = mysqli_query($this->mysqli, "SELECT * FROM crawler where status='OPEN'");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }
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
            return $row['c'];
        } else {
            return false;
        }

    }


    function saveCrawlerTree($parent_id, $child_id)
    {
        mysqli_query($this->mysqli, "INSERT INTO `crawler_tree` (parent_id,child_id) VALUES ('$parent_id','$child_id')");
    }

    function countForProcessedLinks()
    {
        $result = mysqli_query($this->mysqli, "SELECT COUNT(*) as c FROM crawler where status='PROCESSED'");

        if ($row = mysqli_fetch_array($result)) {
            return $row['c'];
        } else {
            return false;
        }
    }

    function getProcessedLink()
    {
        $result = mysqli_query($this->mysqli, "SELECT * FROM crawler where status='PROCESSED'");

        if ($row = mysqli_fetch_array($result)) {
            return $row;
        } else {
            return false;
        }
    }

    function countForLinksInCrawlerTree()
    {
        $result = mysqli_query($this->mysqli, "select count(*) as c from `crawler_tree`");

        if ($row = mysqli_fetch_array($result)) {
            return $row['c'];
        } else {
            return false;
        }
    }

    function countForLinksInCrawlerTreeWhereChild($child_id)
    {
        $result = mysqli_query($this->mysqli, "select count(*) as c from `crawler_tree` where child_id='$child_id'");

        if ($row = mysqli_fetch_array($result)) {
            return $row['c'];
        } else {
            return false;
        }
    }

}