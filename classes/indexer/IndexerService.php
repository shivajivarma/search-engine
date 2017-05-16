<?php

$base = $_SESSION['base'];

require_once $base . '/constants.php';
require_once $base . '/utils/Utils.php';
require_once $base . '/class.database.php';
require_once $base . '/crawler/CrawlerRepository.php';

class IndexerService
{

    private $crawlerRepository;

    function __construct()
    {
        $this->crawlerRepository = new CrawlerRepository();
    }

    function init()
    {
        echo "Indexing initiated";



        $row = $this->crawlerRepository->getFetchedLink();
        if ($row != false) {
            $url = $row['url'];
            $domains = simplexml_load_file("../data/domains.xml");
            if (!$domain = $domains->XPath("/domains/domain[. = '$url']")) {
                $domain = $domains->addChild('domain', $url);
                $domain->addAttribute('id', count($domains));

                $_SESSION['domainID'] = (int)count($domains);

                echo "->>>>>>>>>>" . $_SESSION['domainID'];

                $domains->asXML("../data/domains.xml");
                $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><domain/>");
                $xml->addAttribute('id', $_SESSION['domainID']);
                $xml->asXML("../data/links/" . $_SESSION['domainID'] . ".xml");
            } else {
                $_SESSION['domainID'] = (int)$domain[0]->attributes();
            }
        }

    }


}

?>
