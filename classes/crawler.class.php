<?php
include_once(dirname(__DIR__) . '\classes\crawler\CrawlerService.php');

session_start();
if (isset($_GET['function'])) {
    $crawlerService = new CrawlerService();


    if ($_GET['function'] == 'init') {
        $crawlerService->initializeDatabase();
        echo "Initialized database for :: " . $_GET['url'] . "<br>";
        $crawlerService->saveUrlToCrawler($_GET['url']);
        $crawlerService->displayUnprintedUrls();
    } else if ($_GET['function'] == 'crawlerFetch') {
        $link = $crawlerService->getUnvisitedLink();

        if (!$link) {
            $crawlerService->countLinks();
            die('die-error: Completed');
        }

        echo "Fetching: " . $link['url'] . " -- " . $link['id'] . "<br>";
        $xml = $crawlerService->crawl($link['url']);
        if ((string)$xml->fetch == 'failed') {
            die("die-error: Unable to fetch content:<br>");
        }

        $crawlerService->markLinkAsFetched($link);
        $xml->asXML("indexData/" . $link['id'] . ".xml");
        $_SESSION['link'] = $link;
    } else if ($_GET['function'] == 'crawlerProcess') {
        $crawlerService->crawlerProcess($_SESSION['link']);
    }

}
?>