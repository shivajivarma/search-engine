<?php

class Utils
{
    function __construct(){
        require_once 'constants.php';

        if(PROXY){
            // Define the default, system-wide context.
            $r_default_context = stream_context_get_default (
                array (
                    'http' => array (
                        'proxy' => PROXY_IP.":".PROXY_PORT,
                        'request_fulluri' => True,
                        'timeout' => 8
                    ),
                )
            );
            // Though we said system wide, some extensions need a little coaxing.
            libxml_set_streams_context($r_default_context);
        }
        else{
            // Define the default, system-wide context.
            $r_default_context = stream_context_get_default (
                array (
                    'http' => array (
                        'timeout' => 8
                    ),
                )
            );
            // Though we said system wide, some extensions need a little coaxing.
            libxml_set_streams_context($r_default_context);
        }
    }

    public static function sxe($xml)
    {
        if (is_array($http_response_header) || is_object($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('#^Content-Type: text/xml; charset=(.*)#i', $header, $m)) {
                    switch (strtolower($m[1])) {
                        case 'utf-8':
                            // do nothing
                            break;

                        case 'iso-8859-1':
                            $xml = utf8_encode($xml);
                            break;

                        default:
                            $xml = iconv($m[1], 'utf-8', $xml);
                    }
                    break;
                }
            }
        }


        $temp = simplexml_load_string($xml);


        return $temp;
    }

    public static function fetch($url)
    {
        $file = file_get_contents("$url", 0, stream_context_create()) or false;
        return $file;
    }

    /**
     * Removes script nodes from HTML
     **/
    public static function stripScriptsFromHtml($htmlContent)
    {
        return self::stripByTagNameFromHtml($htmlContent, 'script');
    }

    /**
     * Removes style nodes from HTML
     **/
    public static function stripStylesFromHtml($htmlContent)
    {
        return self::stripByTagNameFromHtml($htmlContent, 'style');
    }


    public static function stripByTagNameFromHtml($htmlContent, $tagName)
    {
        $do = true;
        while ($do) {
            $start = stripos($htmlContent, "<" . $tagName);
            $stop = stripos($htmlContent, "</" . $tagName . ">");
            if ((is_numeric($start)) && (is_numeric($stop))) {
                $htmlContent = substr($htmlContent, 0, $start) . substr($htmlContent, ($stop + strlen("</" . $tagName . ">")));
            } else {
                $do = false;
            }
        }
        return trim($htmlContent);
    }


    public static function splitIt($content)
    { //Spilts the words in the page

        $stopWords = unserialize(file_get_contents("../data/stopWords.dat"));
        $content = str_replace($stopWords, " ", $content);

        $content = preg_replace('/(\s+)\/\*([^\/]*)\*\/(\s+)/s', " ", $content);

        $stopChars = array('&nbsp;', '&copy;', '&amp;', '(', ')', '|', ',', ':', '"', '&', '/', 'Ã', '©', '“', '”', '»');
        $content = str_replace($stopChars, " ", $content);

        $content = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $content);
        $content = preg_replace("/( )+/", " ", $content);
        $content = preg_replace("/( )+$/", "", $content);
        $content = preg_replace("/^( )+/", "", $content);
        return explode(" ", strtolower($content));

    }

}

?>