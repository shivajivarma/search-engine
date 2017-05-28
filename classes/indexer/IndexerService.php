<?php

include_once(dirname(__DIR__) . '\utils\Utils.php');
include_once(dirname(__DIR__) . '\crawler\CrawlerRepository.php');
include_once(dirname(__DIR__) . '\crawler\CrawlerService.php');
include_once(dirname(__DIR__) . '\crawler\model\Crawler.php');
include_once(dirname(__DIR__) . '\domain\DomainService.php');

class IndexerService
{

    private $crawlerRepository, $crawlerService, $domainService;

    function __construct()
    {
        $this->crawlerRepository = new CrawlerRepository();
        $this->crawlerService = new CrawlerService();
        $this->domainService = new DomainService();
    }

    function initialize()
    {
        $row = $this->crawlerRepository->getProcessedLink();
        if ($row) {
            $domainName = $row['url'];
            $domain = $this->domainService->getDomainWithName($domainName);
            $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><domain/>");
            $xml->addAttribute('id', (int)$domain['id']);
            $xml->asXML("../data/links/" . (int)$domain['id'] . ".xml");
            return $domain;
        } else {
            throw new CustomException("Nothing to index.", 400);
        }
    }

    function index($domainId, $domainName)
    {
        $no_of_links = $this->crawlerRepository->countForLinksInCrawlerTree();

        $row = $this->crawlerRepository->getProcessedLink();
        if ($row) {

            $crawledLink = new Crawler($row['id'], $row['url'], $row['status']);

            $xml = $this->crawlerService->getCrawledDataFile($crawledLink , $domainName);
            $title = preg_replace('/^( )*$/', "No title", $xml->title);
            $linksCount = (int)$xml->out->attributes() + (int)$xml->links->attributes();

            $inLinks = $this->crawlerRepository->countForLinksInCrawlerTreeWhereChild($crawledLink->id);


            $outXML = simplexml_load_file("../data/links/" . $domainId . ".xml");

            if (!$link = $outXML->XPath("/domain/link[url = '$crawledLink->url']")) {

                $link = $outXML->addChild('link');

                $_SESSION['linkID'] = count($outXML);
                $link->addAttribute('id', count($outXML));
                $link->url = $crawledLink->url;
                $link->title = $title;
                $link->addAttribute('PageRank', ($inLinks / $no_of_links) * 100);
            } else {
                $att = 'id';
                $_SESSION['linkID'] = (int)$link[0]->attributes()->$att;

                $link[0]->title = $title;
                $att = 'PageRank';
                $link[0]->attributes()->$att = ($inLinks / $no_of_links) * 100;
            }
            $outXML->asXML("../data/links/" . $domainId . ".xml");


            $this->crawlerRepository->updateLinkStatus($crawledLink->id, 'INDEXED');


            IndexerService::indexIt($xml, $domainId, $_SESSION['linkID']);

            return $crawledLink;
        }

        return false;
    }

    static function indexIt($xml, $domainId, $linkId)
    {


        $keywords = simplexml_load_file("../data/keywords.xml");
        foreach ($xml->words->children() as $word) {

            if (!$keyword = $keywords->XPath("/keywords/keyword[. = '$word']")) {
                $keyword = $keywords->addChild('keyword', (string)$word);
                $keywordID = count($keywords);
                $keyword->addAttribute('id', $keywordID);
            } else {
                $att = 'id';
                $keywordID = $keyword[0]->attributes()->$att;
            }


            $occur = $word->attributes();
            $word = (string)$word;
            IndexerService::insert($keywordID, $word[0], $domainId, $linkId, $occur[0]);

        }


        $keywords->asXML("../data/keywords.xml");
    }


    static function insert($keywordID, $char, $domainId, $linkId, $occur)
    {


        if (preg_match('/^[a-z0-9]$/i', $char)) $path = "../data/database/" . $char . ".xml";
        else $path = "../data/database/others.xml";

        $xml = simplexml_load_file($path);

        if (!$keyword = $xml->XPath("/data/keyword[@id = '$keywordID']")) {

            $keyword = $xml->addChild('keyword');
            $keyword->addAttribute('id', $keywordID);
        }


        if (!$link = $keyword[0]->XPath("/link[@id = '$linkId' and @domain = '$domainId']")) {
            $link = $keyword[0]->addChild('link');
            $link->addAttribute('id', $linkId);
            $link->addAttribute('domain', $domainId);
            $link->addAttribute('occur', $occur);
        } else {
            $att = 'occur';
            $link[0]->attributes()->$att = $occur;
        }


        $xml->asXML($path);
    }


}
