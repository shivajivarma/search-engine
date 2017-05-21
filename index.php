<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>Search Engine</title>

    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="css/default.css" type="text/css">
</head>

<body>
<div id="header">

    <span id="title" onclick="location. href='./'">Search Engine</span>

    <form method="get">
        <table>
            <tr>
                <td><input id="input" type="text" value="<?php if (isset($_GET['query'])) echo $_GET['query']; ?>"
                           name="query"></td>
                <td><input class="submit" value="Search" type="submit"></td>
            </tr>
        </table>
    </form>

    <div id="menu">
        <table>
            <tr>
                <td><a href="./crawler.php">Crawler</a></td>
                <td><a href="indexer.php">Indexer</a></td>
                <td><a href="classes/proxy.php">Proxy</a></td>
                <td><a href="data/domains.xml">List of sites</a></td>
            </tr>
        </table>
    </div>
</div>


<div id='suggestions'>
    <img title="loader" src="images/preloader.gif" class="loader">
    <div class="words">Hello!! Start searching more efficiently.</div>
</div>


<div id="container"></div>


<div id='footer'>
    <div id='copyright'>Final Year Project 2012 &nbsp;</div>
</div>

<script type="text/javascript" src="./scripts/jquery.js"></script>
<script type="text/javascript">


    $(document).ready(function () {


        var $container = $('#container'),
            suggestions = $('#suggestions');

        if (document.getElementsByName("query")[0].value) {
            $.ajax({
                type: "GET",
                url: "api/search.api.php?query=" + document.getElementsByName("query")[0].value,
                dataType: "xml",
                success: function (xml) {
                    $container.html("");

                    var redirect = "false";


                    $(xml).find('specialResult').each(function () {
                        var title = $(this).find('title').text();
                        var url = $(this).find('url').text();
                        var urlID = $(this).find('url').attr('id');
                        var domainID = $(this).find('url').attr('domain');
                        var cat = $(this).find('category').text();


                        var output = "<div class='specialResult-div'><div class='specialResult'> <a href='./open.page.php?urlID=" + urlID + "&&redirect=true&&domainID=" + domainID + "'>" + title + "</a><div class='link'>" + url + "</div><div class='category'>" + cat + "</div><div class='report' onclick=\"location. href='./unlike.php?cat=" + cat + "&&domainID=" + domainID + "'\">report</div></div></div>"


                        $container.append(output);
                        redirect = "true";
                    });


                    cat = $(xml).find('specialResults').attr("caterogy");


                    $(xml).find('result').each(function () {
                        var title = $(this).find('title').text();
                        var url = $(this).find('url').text();
                        var urlID = $(this).find('url').attr('id');
                        var domainID = $(this).find('url').attr('domain');


                        var weight = $(this).find('weight').attr('value');
                        var PageRank = $(this).find('weight').attr('PageRank');
                        var hits = $(this).find('weight').attr('hits');


                        var output = "<div class='result-div'><div class='result'> <a href='./open.page.php?urlID=" + urlID + "&&domainID=" + domainID + "&&redirect=" + redirect + "&&cat=" + cat + "'>" + title + "</a><div class='link'>" + url + "</div><div class='weight'>[Weight: " + weight + "] [Hits: " + hits + "]  [Page rank: " + PageRank + "]</div></div></div>"

                        $container.append(output);
                    });

                    if (!( $(xml).find('specialResult').length + $(xml).find('result').length)) {
                        $container.append("<div style='font-size:50px; text-align:center; margin-top:200px'>No results<div>");
                    }

                }
            });

            $.ajax({
                type: "GET",
                url: "api/suggestions.api.php?query=" + document.getElementsByName("query")[0].value,
                dataType: "json",
                success: function (json) {
                    suggestions.find('.loader').css('display', 'none');
                    suggestions.find('.words').html(JSON.stringify(json));

                    if (json) {
                        if (json[1]) {
                            suggestions.find('.words').html('Suggestions : ');
                            var i = 0;
                            for (i = 0; i < 5; i++) {
                                suggestions.find('.words').append('<a href="./?query=' + json[1][i] + '"><u>' + json[1][i] + '</u></a>  ');
                            }
                        } else {
                            suggestions.find('.words').text('No suggestions');
                        }
                    } else {
                        suggestions.find('.words').text('Service unavailable');
                    }
                },
                failure: function () {
                    suggestions.find('.words').text('Service unavailable');
                }
            });
        } else {
            suggestions.find('.loader').css('display', 'none');
        }

    });
</script>

</body>
</html>