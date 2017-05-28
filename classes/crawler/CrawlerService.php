<?php

include_once(dirname(__DIR__) . '\utils\Utils.php');
include_once(dirname(__DIR__) . '\crawler\CrawlerRepository.php');
include_once(dirname(__DIR__) . '\crawler\model\Crawler.php');
include_once(dirname(__DIR__) . '\domain\DomainService.php');

class CrawlerService
{

    private $crawlerRepository, $domainService;

    private static $CRAWLER_DATA_PATH = '../data/crawler/';

    function __construct()
    {
        $this->crawlerRepository = new CrawlerRepository();
        $this->domainService = new DomainService();
    }


    function initializeDatabase()
    {
        $this->crawlerRepository->dropCrawlerSchema();
        $this->crawlerRepository->createCrawlerSchema();
    }

    function prepareDomain($url){
        $domainName = Utils::getDomainFromUrl($url);


        $domain = $this->domainService->saveDomain($domainName);

        $path = $this::$CRAWLER_DATA_PATH.$domainName;
        Utils::removeFolder($path);
        Utils::createFolder($path);

        return $domain;

    }


    function saveUrlToCrawler($url)
    {
        $this->crawlerRepository->saveCrawler($url);
    }




    function saveCrawledDataToFile($xml, $link, $domainName){
        $this->crawlerRepository->updateLinkStatus($link->id, 'FETCHED');
        $xml->asXML($this::$CRAWLER_DATA_PATH . $domainName . '/'.$link->id . ".xml");
    }

    function getCrawledDataFile($link, $domainName){
        return simplexml_load_file($this::$CRAWLER_DATA_PATH . $domainName . '/'.$link->id . ".xml");
    }


    function fetchFailed($link){
        $this->crawlerRepository->updateLinkStatus($link->id, 'FETCH_FAILED');
    }


    static function crawl($url)
    {


        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><crawler/>");

        if (!(!$file = Utils::fetch("http://" . preg_replace('/^http[s]?:(\/)(\/)(www.)?/', "$3", $url)))) {
            $xml->fetch = "true";
            $xml->url = "http://" . preg_replace('/^http[s]?:(\/)(\/)/', "", $url);
            $domain = parse_url("http://" . preg_replace('/^http[s]?:(\/)(\/)/', "", $url));
            if (!isset($domain['path'])) {
                $domain['path'] = '';
            }
            $xml->domain = $domain['host'];
            $xml->path = preg_replace('/(\/)[a-zA-z0-9\.]+$/', "", preg_replace('/(\/)$/', "", $domain['path']));
            $xml->path = preg_replace('/(\/([a-z0-9])+)(\/\.\.)$/i', "", $xml->path);


            self::lookForLinks($xml, $file);

            $xml->links->addAttribute('count', count($xml->links[0]));
            $xml->out->addAttribute('count', count($xml->out[0]));
            unset($xml->out->link);

            self::lookForWords($xml, $file);

            $xml->words->addAttribute('count', count($xml->words[0]));

        } else {
            $xml->fetch = "failed";
        }

        return $xml;
    }

    static function lookForLinks($xml, $file)
    {
        $links = $xml->addChild('links');
        $out = $xml->addChild('out');
        $stripped_file = strip_tags($file, "<a>");
        preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $stripped_file, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < sizeof($matches); $i++) {
            $url = $matches[$i][1];
            if (!($url == "#" || preg_match('/^\/\//i', $url) || preg_match('/^mailto:/i', $url) || preg_match('/^javascript:/i', $url) || preg_match('/(\.(pdf)?(png)?(jpg)?(ppt)?(doc)?)$/i', $url))) {

                $url = preg_replace('/^(\/)/', $xml->domain . "/", $url);
                $url = preg_replace('/^([a-zA-z0-9]*\/)/', $xml->domain . $xml->path . '/$0', $url);
                $url = preg_replace('/^(\.\/)(\.\.\/)(\.\.\/)/', $xml->domain . preg_replace('/(\/)[a-z0-9]+$/i', "", preg_replace('/(\/)[a-z0-9]+$/i', "", $xml->path)) . "/", $url);
                $url = preg_replace('/^(\.\/)(\.\.\/)/', $xml->domain . preg_replace('/(\/)[a-z0-9]+$/i', "", $xml->path) . "/", $url);
                $url = preg_replace('/^(\.\/)/', $xml->domain . $xml->path . "/", $url);
                $url = preg_replace('/^(\.\.\/)(\.\.\/)/', $xml->domain . preg_replace('/(\/)[a-z0-9]+$/i', "", preg_replace('/(\/)[a-z0-9]+$/i', "", $xml->path)) . "/", $url);
                $url = preg_replace('/^(\.\.\/)/', $xml->domain . preg_replace('/(\/)[a-z0-9]+$/i', "", $xml->path) . "/", $url);


                if (strstr($url, (string)$xml->domain) == true) {
                    if (!$xml->XPath("/crawler/links/link[. = '$url']"))
                        $links->addChild('link', $url);
                } else if (!$xml->XPath("/crawler/out/link[. = '$url']"))
                    $out->addChild('link', $url);
            }
        }

        for ($i = 0; $i < sizeof($xml->links[0]); $i++) {
            $xml->links[0]->link[$i] = preg_replace('/^(http[s]?:(\/)(\/))?(www.)?/i', "$4", $xml->links[0]->link[$i]);
        }
    }


    static function lookForWords($xml, $file)
    {
        preg_match("/<title>(.*)<\/title>/siU", $file, $title);


        if (isset($title[1])) {
            $xml->title = $title[1];
        }

        $xmlWords = $xml->addChild('words');

        $file = Utils::stripScriptsFromHtml($file);  //Removes scripts from the webpage.
        $file = Utils::stripStylesFromHtml($file);  //Removes style from the webpage.


        $file = strip_tags($file);  //Trims all tags.
        $tokens = Utils::splitIt($file);    //Spilts the words in the page.
        $words = array_count_values($tokens);

        foreach ($words as $word => $count) {
            $word = urlencode($word);
            $word = $xmlWords->addChild('word', $word);
            $word->addAttribute('count', $count);
        }

    }


    function getUnvisitedLink()
    {
        $link = $this->crawlerRepository->fetchOpenLink();
        if ($link) {
            $this->crawlerRepository->updateLinkStatus($link['id'], 'FETCHING');
            return new Crawler($link['id'], $link['url'], $link['status']);
        }
        return null;
    }



    function crawlerProcess($linkId, $domainName)
    {
        $xml = simplexml_load_file($this::$CRAWLER_DATA_PATH . $domainName . '/'. $linkId . ".xml");
        $links = $xml->links[0];


        $result = new stdClass();
        $result->totalLinks = (int)$links->attributes();
        $result->totalLinks = $result->totalLinks + (int)$xml->out->attributes();
        $result->links = array();

        $result->totalNewLinks = 0;
        for ($i = 0; $i < count($links); $i++) {
            $url = $links->link[$i];

            $row = $this->crawlerRepository->fetchByUrl($url);

            if (!$row['url']) {
                $newLink = new stdClass();
                $newLink->id = $this->crawlerRepository->saveCrawler($url);
                $newLink->url= strval($url);

                array_push($result->links, $newLink);

                $childId = $this->crawlerRepository->fetchCount();
                $result->totalNewLinks++;
            } else {
                $childId = $row['id'];
            }

            $this->crawlerRepository->saveCrawlerTree($linkId, $childId);
        }

        $this->crawlerRepository->updateLinkStatus($linkId, 'PROCESSED');

        return $result;
    }


    function countLinks()
    {
        return $this->crawlerRepository->countForProcessedLinks();
    }


}

?>
