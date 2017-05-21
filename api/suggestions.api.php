<?php

include_once(dirname(__DIR__) . '\classes\constants.php');

header('Content-type: application/json');


if (!isset($_GET['query'])) {
    echo "{}";
} else {


    $lang = 'en';
    $url = 'http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=' . $lang . '&q=' . urlencode($_GET['query']);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.0; rv:2.0.1) Gecko/20100101 Firefox/4.0.1");
    if (PROXY) {
        curl_setopt($ch, CURLOPT_PROXY, PROXY_IP);
        curl_setopt($ch, CURLOPT_PROXYPORT, PROXY_PORT);
    }
    $data = curl_exec($ch);
    curl_close($ch);

    echo $data;
}
?>