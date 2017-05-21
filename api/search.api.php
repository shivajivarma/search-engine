<?php
header('Content-type: text/xml');

$xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><results/>");

if (isset($_GET['query']) && $_GET['query'] != '') {

    $query = $_GET['query'];
    $search = new search();


    $words = $search->splitIntoWords($query);    //Splits input query into keywords;
    $numberOfWords = sizeof($words);
    $keywordsXML = simplexml_load_file("../data/keywords.xml"); //Import all exsisting keywords


    $domainsXML = simplexml_load_file("../data/domains.xml");
    $specialID = 0;
    $cat = "home";


    foreach ($words as $keyword) {
        if ($specialID == 0)
            foreach ($domainsXML->XPath("/domains/domain") as $domain) {
                $att = "name";
                if (strtolower($keyword) == (string)$domain[0]->attributes()->$att) {
                    $specialID = $domain[0]->attributes();
                    break;
                }
            }


        if ($keywordId = $search->searchKeyword($keyword, $keywordsXML)) {    //Searchs keyword in list and returns 'ID', if exists;


            $databaseXML = simplexml_load_file("../data/database/$keyword[0].xml");
            $links = $databaseXML->XPath("/data/keyword[@id=$keywordId]");

            //    print_r($links);

            foreach ($links[0]->children() as $link) {
                $att = 'id';
                $urlID = $link->attributes()->$att;

                $att = 'domain';
                $domainID = $link->attributes()->$att;


                $att = 'occur';
                $occurrence = $link->attributes()->$att;

                if (!$result = $xml->XPath("/results/result[url/@id = $urlID and url/@domain = $domainID]")) {
                    //Insert new result;
                    $new_result = $xml->addChild('result');
                    $new_result->addChild('title');
                    $url = $new_result->addChild('url');
                    $url->addAttribute('id', $urlID);
                    $url->addAttribute('domain', $domainID);

                    $weight = $new_result->addChild('weight');
                    $words = $new_result->addChild('words');
                    $word = $words->addChild('word', $keyword);
                    $word->addAttribute('count', $occurrence);
                } else {
                    //Modify old result;
                    $words = $result[0]->words;
                    $word = $words->addChild('word', $keyword);
                    $word->addAttribute('count', $occurrence);
                }

            }


        }
    }


    //Fetch title and url of the links and calculate weights
    foreach ($xml->children() as $child) {
        $att = 'id';
        $urlID = $child->url->attributes()->$att;

        $att = 'domain';
        $domainID = $child->url->attributes()->$att;


        $domainXML = simplexml_load_file("../data/links/" . (int)$domainID . ".xml");
        $url = $domainXML->XPath("/domain/link[@id = '$urlID']");

        $child->url = $url[0]->url;
        $child->title = $url[0]->title;


        $weight = 0;
        $words = $child->words;
        $count = count($child->words[0]);
        for ($i = 0; $i < pow(2, $count); $i++) {
            $temp = $i;
            /* Converts the decimal number into binary and stores each digit in status of arr  */
            for ($j = $count - 1; $j >= 0; $j--) {
                $arr[$j] = $temp % 2;
                $temp = $temp / 2;
            }

            $att = 'count';
            $min = $words->word[0]->attributes()->$att;
            $one = 0;
            for ($j = $count - 1; $j >= 0; $j--) {
                $att = 'count';
                if ($arr[$j]) {
                    $occur = $words->word[$j]->attributes()->$att;

                    if ($min > $occur) $min = $occur;
                    $one++;
                }
            }
            $weight = $weight + ($min * $one);
        }
        $child->weight->addAttribute("value", $weight);
        $att = 'PageRank';
        $child->weight->addAttribute("PageRank", $url[0]->attributes()->$att);

        $att = 'hits';
        if (!$url[0]->attributes()->$att) $hits = 0;
        else $hits = (int)$url[0]->attributes()->$att;
        $child->weight->addAttribute("hits", $hits);


        //unset($child->words);
    }


    // Sort results;
    $count = count($xml);
    //echo $count;
    for ($i = 0; $i <= $count - 2; $i++)
        for ($j = $i + 1; $j <= $count - 1; $j++) {
            $att = 'value';
            $a['value'] = (int)$xml->result[$i]->weight->attributes()->$att;
            $b['value'] = (int)$xml->result[$j]->weight->attributes()->$att;

            $att = 'hits';
            $a['hits'] = (int)$xml->result[$i]->weight->attributes()->$att;
            $b['hits'] = (int)$xml->result[$j]->weight->attributes()->$att;

            $att = 'PageRank';
            $a['rank'] = (int)$xml->result[$i]->weight->attributes()->$att;
            $b['rank'] = (int)$xml->result[$j]->weight->attributes()->$att;

            if ($a['value'] < $b['value'] || ($a['value'] == $b['value'] && $a['hits'] < $b['hits']) || ($a['value'] == $b['value'] && $a['hits'] == $b['hits'] && $a['rank'] < $b['rank']))
                $search->exchange($xml, $i, $j);

        }


    // Results based on knowledge

    if ($specialID != 0) {
        $special = $xml->addChild("specialResults");

        $catergoryXML = simplexml_load_file("../data/rules.xml");
        foreach ($catergoryXML->XPath("/categories/category") as $category) {
            foreach ($category->children() as $child)
                if ($cat == "home") {
                    if (strstr((string)$search->purify($query), strtolower($child))) {
                        $att = 'tag';
                        $cat = (string)$category[0]->attributes()->$att;
                        break;
                    }
                    //echo $child."==".$search->purify($query)."\n";
                }
        }


        $domainXML = simplexml_load_file("../data/links/" . (int)$specialID . ".xml");
        if ($catID = $domainXML->XPath("/domain/$cat")) {
            $att = 'link';
            if ($catID[0]->attributes()->$att) {
                $result = $special->addChild("specialResult");
                $urlID = $catID[0]->attributes();
                $url = $domainXML->XPath("/domain/link[@id = '$urlID']");

                $result->category = $cat;
                $result->url = $url[0]->url;
                $result->title = $url[0]->title;
                $result->url->addAttribute("id", $urlID);
                $result->url->addAttribute("domain", $specialID);
            }
        }

        $special->addAttribute("caterogy", $cat);
    }


}


print($xml->asXML());

//echo json_encode($xml);
?>
<?php

class search
{

    //Splits input query into keywords;
    function splitIntoWords($input)
    {
        $stopChar = array('.', ',');        //Remove waste characters
        $output = str_replace($stopChar, " ", $input);
        $output = preg_replace("/( )+/", " ", $output);
        $output = preg_replace("/^( )+/", "", $output);
        $output = preg_replace("/( )+$/", "", $output);
        return array_unique(explode(" ", $output));
    }


    function purify($input)
    {
        $output = preg_replace("/( )+/", " ", $input);
        $output = preg_replace("/^( )+/", "", $output);
        $output = preg_replace("/( )+$/", "", $output);
        return $output;
    }


    //Searchs keyword in list and returns 'ID', if exists;
    function searchKeyword($input, $xml)
    {


        foreach ($xml->children() as $child) {
            $att = 'id';
            if (!strcasecmp($child, $input)) return $child->attributes()->$att;

        }
        return false;
    }

    function exchange($xml, $a, $b)
    {
        $temp = (string)$xml->result[$a]->title;
        $xml->result[$a]->title = $xml->result[$b]->title;
        $xml->result[$b]->title = $temp;

        $temp = (string)$xml->result[$a]->url;
        $xml->result[$a]->url = $xml->result[$b]->url;
        $xml->result[$b]->url = $temp;

        $att = 'id';
        $temp = (string)$xml->result[$a]->url->attributes()->$att;
        $xml->result[$a]->url->attributes()->$att = $xml->result[$b]->url->attributes()->$att;
        $xml->result[$b]->url->attributes()->$att = $temp;

        $att = 'domain';
        $temp = (string)$xml->result[$a]->url->attributes()->$att;
        $xml->result[$a]->url->attributes()->$att = $xml->result[$b]->url->attributes()->$att;
        $xml->result[$b]->url->attributes()->$att = $temp;

        $att = 'value';
        $temp = (string)$xml->result[$a]->weight->attributes()->$att;
        $xml->result[$a]->weight->attributes()->$att = $xml->result[$b]->weight->attributes()->$att;
        $xml->result[$b]->weight->attributes()->$att = $temp;

        $att = 'PageRank';
        $temp = (string)$xml->result[$a]->weight->attributes()->$att;
        $xml->result[$a]->weight->attributes()->$att = $xml->result[$b]->weight->attributes()->$att;
        $xml->result[$b]->weight->attributes()->$att = $temp;

        $att = 'hits';
        $temp = (string)$xml->result[$a]->weight->attributes()->$att;
        $xml->result[$a]->weight->attributes()->$att = $xml->result[$b]->weight->attributes()->$att;
        $xml->result[$b]->weight->attributes()->$att = $temp;
    }


}

?>