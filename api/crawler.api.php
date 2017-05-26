<?php
include(dirname(__DIR__) . '\classes\crawler\CrawlerService.php');

header('Content-type: text/xml');

$xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><crawler/>");
if (isset($_GET['url'])) {
    $xml = CrawlerService::crawl($_GET['url']);
}
print($xml->asXML());


?>