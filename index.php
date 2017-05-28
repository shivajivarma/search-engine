<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Search Engine</title>

    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-toggleable-md navbar-light">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
            data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="/#">Search Engine</a>


    <form class="form-inline mr-auto">
        <input class="form-control mr-sm-2" type="text" name="query" placeholder="Search"
               value="<?php if (isset($_GET['query'])) echo $_GET['query']; ?>">
        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
    </form>


    <div class="collapse navbar-collapse float-right" id="indexNavar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="crawler.html">Crawler</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="indexer.html">Indexer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="proxy.html">Proxy Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="data/domains.xml">List of sites</a>
            </li>
        </ul>
    </div>
</nav>


<div id='suggestions' class="container-fluid">
    <div class="row">
        <div class="col-1 loader">
            <img title="loader" src="assets/images/preloader.gif">
        </div>
        <div class="col-11">
            <div class="words">Hello!! Start searching more efficiently.</div>
        </div>
    </div>
</div>


<div id="container" class="container-fluid"></div>


<footer class="footer">
    <div class="container-fluid">
        <span class="text-muted"> ALL WORKS &copy; <script
                    type="text/javascript">document.write(new Date().getFullYear())</script> <a target="_blank"
                                                                                                href="//shivajivarma.com/?rel=author"
                                                                                                rel="author">SHIVAJI VARMA</a></span>
    </div>
</footer>


<script type="text/javascript" src="assets/scripts/jquery.js"></script>
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


                        var output = "<div class='result-div row'>" +
                                            "<div class='col-md-8'>" +
                                                "<a href='./open.page.php?urlID=" + urlID + "&&domainID=" + domainID + "&&redirect=" + redirect + "&&cat=" + cat + "'>" + title + "</a>" +
                                                "<div class='link'>" + url + "</div>" +
                                            "</div>" +
                                            "<div class='col-md-4'>" +
                                                "<div class='weight'>[Weight: " + weight + "] [Hits: " + hits + "]  [Page rank: " + PageRank + "]</div>" +
                                            "</div>" +
                                        "</div>";

                        $container.append(output);
                    });

                    if (!( $(xml).find('specialResult').length + $(xml).find('result').length)) {
                        $container.append("<div class='no-result'>No results</div>");
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
                        if (json[1] && json[1].length) {
                            suggestions.find('.words').html('Suggestions : ');
                            var i = 0;
                            for (i = 0; i < 5 && i < json[1].length; i++) {
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