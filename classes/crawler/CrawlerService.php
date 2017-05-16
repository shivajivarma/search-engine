<?php

$base = $_SESSION['base'];

require_once $base . '/constants.php';
require_once $base . '/utils/Utils.php';
require_once $base . '/class.database.php';
require_once $base . '/crawler/CrawlerRepository.php';

class CrawlerService
{

    private $crawlerRepository;

    function __construct()
    {
        $this->crawlerRepository = new CrawlerRepository();
    }


    function initializeDatabase()
    {
        $this->crawlerRepository->dropCrawlerSchema();
        $this->crawlerRepository->createCrawlerSchema();
    }


    function saveUrlToCrawler($url)
    {
        $this->crawlerRepository->saveCrawler($url);
    }

    function displayUnprintedUrls()
    {
        $result = $this->crawlerRepository->fetchAllCrawlerForPrint(0);

        while ($row = mysqli_fetch_array($result)) {
            echo $row['id'] . " " . $row['url'];
            echo "<br>";
        }

        $this->crawlerRepository->updateAllCrawlerToPrinted();
    }


    function crawl($url)
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


            $this->lookForLinks($xml, $file);

            $xml->links->addAttribute('count', count($xml->links[0]));
            $xml->out->addAttribute('count', count($xml->out[0]));
            unset($xml->out->link);

            $this->lookForWords($xml, $file);

            $xml->words->addAttribute('count', count($xml->words[0]));

        } else {
            $xml->fetch = "failed";
        }

        return $xml;
    }

    function lookForLinks($xml, $file)
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


    function lookForWords($xml, $file)
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
        $link = $this->crawlerRepository->fetchUnvisitedLink();
        if($link){
            $this->crawlerRepository->updateLinkAsVisited($link['id']);
        }
        return $link;
    }

    function markLinkAsFetched($link)
    {
        $this->crawlerRepository->updateLinkAsFetched($link['id']);
    }


    function crawlerProcess($link)
    {
        $xml = simplexml_load_file("indexData/" . $link['id'] . ".xml");
        $links = $xml->links[0];
        $count = (int)$links->attributes();
        $count = $count + (int)$xml->out->attributes();
        echo "Total links in the page:" . $count . "<br>";


        $count = 0;
        for ($i = 0; $i < count($links); $i++) {
            $url = $links->link[$i];

            $row = $this->crawlerRepository->fetchByUrl($url);

            if (!$row['url']) {
                $this->crawlerRepository->saveCrawler($url);
                $row = $this->crawlerRepository->fetchCount();
                $child_id = $row['c'];
                $count++;
            } else {
                $child_id = $row['id'];
            }

            $this->crawlerRepository->saveCrawlerTree($link['id'], $child_id);
        }

        echo "New links in the page:" . $count . "<br>";
        $this->displayUnprintedUrls();
    }


    function countLinks(){
        $row =  $this->crawlerRepository->countForFetchedLinks();
        echo "Number of links collected:" . $row['c'] . "<br>";
    }


}

?>
