<?php
header('Content-type: text/xml');
$_SESSION['base'] = '../classes';
require('../classes/crawler/CrawlerService.php');
$xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><crawler/>");
if (isset($_GET['url'])) {
    $crawlerService = new CrawlerService();
    $xml = $crawlerService->crawl($_GET['url']);
}
print($xml->asXML());
?>