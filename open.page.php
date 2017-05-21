<?php


if (isset($_GET['urlID']) && isset($_GET['domainID']) && isset($_GET['redirect'])) {
    $domainId = $_GET['domainID'];
    $urlId = $_GET['urlID'];
    $cat = $_GET['cat'];


    $domainXML = simplexml_load_file("./data/links/" . $domainId . ".xml");
    $url = $domainXML->XPath("/domain/link[@id = '$urlId']");
    $att = 'hits';
    if (!$url[0]->attributes()->$att) $url[0]->addAttribute('hits', 1);
    else $url[0]->attributes()->$att = (int)$url[0]->attributes()->$att + 1;
    $domainXML->asXML("./data/links/" . $domainId . ".xml");

    if ($_GET['redirect'] == "true" || $_GET['cat'] == "undefined") {
        header("Location: http://" . $url[0]->url);
    }


    if (isset($_GET['option'])) {
        if ($_GET['option'] == "yes") {
            if (!$child = $domainXML->XPath("/domain/$cat")) {
                $child = $domainXML->addChild($cat);
                $child->addAttribute('link', $urlId);
            }
        }

        $domainXML->asXML("./data/links/" . $domainId . ".xml");

        echo "Thank you";
    }

}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>Project</title>

    <link rel="stylesheet" href="./css/default.css" type="text/css">

    <script type="text/javascript" src="./scripts/jquery.js"></script>

</head>

<body>
<div id="header">

    <span id="title" onclick="location. href='./'">Help us </span>

    <form method="get">
        <table id="question">
            <tr>
                <td> If this "<?php echo $cat; ?>" page:<input type="radio" value="yes" name="option">Yes <input
                            type="radio" value="no" name="option">No
                </td>
                <td><input class="submit" type="submit">

                </td>
            </tr>
        </table>

        <input type="hidden" value="<?php echo $_GET['urlID']; ?>" name="urlID">
        <input type="hidden" value="<?php echo $_GET['domainID']; ?>" name="domainID">
        <input type="hidden" value="<?php echo $_GET['redirect']; ?>" name="redirect">
        <input type="hidden" value="<?php echo $_GET['cat']; ?>" name="cat">


    </form>


</div>

<div id="skip-button" onclick="<?php echo ("location. href='http://" . $url[0]->url) . "'"; ?>">Skip</div>

<iframe src="<?php echo "http://" . $url[0]->url; ?>"></iframe>

</body>
</html>
