<?php
require_once 'funcs.php';
$funcs = new funcs();
$query = $_GET['query'];
if ($query == "") {
    echo 'Hello!! Start searching more efficiently.';
    return;
}


/*$var = file_get_contents("http://didyoumean.info/api?q=".urlencode($query));

if($var) {
    echo "Do you mean: <a href='./?query=$var'><u>$var</u></a> ?";
    return;
}
 */


$lang = 'en';
$url = 'http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=' . $lang . '&q=' . urlencode($query);

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

$suggestions = json_decode($data, true);

if ($suggestions) {
    if (sizeof($suggestions[1])) {
        echo 'Suggestions : ';
        $i = 0;
        foreach ($suggestions[1] as $var) {
            if ($i == 5) break;
            echo "<a href='./?query=$var'><u>$var</u></a>  ";
            $i++;
        }
    } else echo 'No suggestions';
} else echo 'Service unavailable';
?>