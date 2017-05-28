<?php

include_once(dirname(__DIR__) . '\crawler\CrawlerService.php');

class CrawlerController
{

    private $crawlerService;

    function __construct()
    {
        $this->crawlerService = new CrawlerService();
    }

    function crawlUrl()
    {
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><crawler/>");
        if (isset($_GET['url'])) {
            $xml = CrawlerService::crawl($_GET['url']);
        }
        header('Content-type: text/xml');
        print($xml->asXML());
    }


    function startCrawlDomain(){
        $this->crawlerService->initializeDatabase();

        $domain = $this->crawlerService->prepareDomain($_GET['url']);

        if($domain){
            $this->crawlerService->saveUrlToCrawler(strval($domain));

            $response = new stdClass();
            $response->databaseInitialized = true;
            $response->domain = new stdClass();
            $response->domain->id = strval($domain['id']);
            $response->domain->name = strval($domain);
        } else {
            throw new CustomException("No Such domain");
        }

        header('Content-type: text/json');
        echo json_encode($response);
    }


    function fetch(){

        $link = $this->crawlerService->getUnvisitedLink();

        $response = new stdClass();
        if (!$link) {
            $response->count = $this->crawlerService->countLinks();
            $response->status = 'complete';
        } else {

            $xml = $this->crawlerService->crawl($link->url);

            if ((string)$xml->fetch == 'failed') {
                $this->crawlerService->fetchFailed($link);
                $response->status = 'failed';
                $response->message = '404 : requested page not found.';
            } else {
                $this->crawlerService->saveCrawledDataToFile($xml, $link, $_GET['domainName']);
                $response->status = 'fetched';
            }
            $response->link = $link;
        }

        header('Content-type: text/json');
        echo json_encode($response);
    }


    function process(){
        $response = $this->crawlerService->crawlerProcess($_GET['id'], $_GET['domainName']);
        header('Content-type: text/json');
        echo json_encode($response);
    }



}

?>
