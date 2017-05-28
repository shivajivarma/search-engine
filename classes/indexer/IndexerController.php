<?php

include_once(dirname(__DIR__) . '\indexer\IndexerService.php');

class IndexerController
{

    private $indexerService;

    function __construct()
    {
        $this->indexerService = new IndexerService();
    }


    function startIndexingDomain(){
        $domain = $this->indexerService->initialize();
        $response = new stdClass();
        $response->initialized = true;
        $response->domain = new stdClass();
        $response->domain->id = strval($domain['id']);
        $response->domain->name = strval($domain);

        header('Content-type: text/json');
        echo json_encode($response);
    }

    function indexIt(){
        $link = $this->indexerService->index($_GET['domainId'], $_GET['domainName']);

        $response = new stdClass();
        if($link){
            $response->link = $link;
            $response->status = 'indexed';
        } else {
            $response->status = 'complete';
        }

        header('Content-type: text/json');
        echo json_encode($response);
    }

}

?>
