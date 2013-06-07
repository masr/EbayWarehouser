<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>eBay Local Fulfillment Platform - eLFP</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="css/main.css">

    <script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser
    today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better
    experience this site.</p>
<![endif]-->

<!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

<div class="navbar navbar-static-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="#">eLFP</a>

            <div class="nav-collapse collapse">
                <form class="navbar-search pull-left">
                    <input type="text" class="search-query" placeholder="Search">
                </form>
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li class="nav-header">Nav header</li>
                            <li><a href="#">Separated link</a></li>
                            <li><a href="#">One more separated link</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Example row of columns -->
    <div class="row-fluid">
        <div class="span3 aside">
            <div class="accordion filters" id="filter">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#filter" href="#filter-form"
                           id="recommendation-link">
                            Intelligent Recommendation
                        </a>
                    </div>
                    <div id="filter-form" class="accordion-body collapse">
                        <div class="accordion-inner">

                            <div class="btn-group" data-toggle="buttons-radio">
                                <button class="btn recommend_button">1</button>
                                <button class="btn recommend_button">2</button>
                                <button class="btn recommend_button">3</button>
                                <button class="btn recommend_button">4</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <footer>
                <hr/>
                <p>&copy; eBay Inc. 2012</p>
            </footer>
        </div>
        <div class="span9 map_wrapper">
            <div id="map_canvas" class="map_canvas"></div>
        </div>
    </div>


</div>
<!-- /container -->

<div id="loginModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="loginModalLabel">Please Login</h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" id="loginForm">
            <div class="control-group">
                <label class="control-label" for="inputSellerId">Seller ID</label>

                <div class="controls">
                    <input type="text" id="inputSellerId" placeholder="Seller ID" name="seller_id">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword">Password</label>

                <div class="controls">
                    <input type="password" id="inputPassword" placeholder="Password" name="password">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox"> Remember me
                    </label>
                    <button type="submit" class="btn">Sign in</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="js/vendor/jquery-1.8.2.min.js"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script>
    $(function () {
        function queryStrings() {//get url querystring
            var params = document.location.search, reg = /(?:^\?|&)(.*?)=(.*?)(?=&|$)/g, temp, args = {};
            while ((temp = reg.exec(params)) != null) args[temp[1]] = decodeURIComponent(temp[2]);
            return args;
        }

        var req = queryStrings();
        var ltn = $.trim(req['lat']);
        var lng = $.trim(req['lng']);
        var location = $.trim(req['location']);
        var seller_id = $.trim(req['seller_id']);


        $("#map_canvas").VMap().on('vmap.initComplete', function (event, vMap) {
            if (ltn.length > 0 && lng.length > 0) {
                vMap.createUserPosition(ltn, lng);
            }
            vMap.drawAllWarehouses();

            vMap.drawAllTrans(seller_id);
            $(".recommend_button").click(function () {
                var num = parseInt(this.innerHTML, 10);
                $.getJSON('api.php', {seller_id:seller_id, num:num, type:'recommend_houses'}, function (data) {
                    vMap.clearHouseMarkers();
                    $.each(data, function (index, record) {
                        vMap.createWarehouse(record['latitude'], record['longtitude'], record);
                    });
                    vMap.drawAllTrans(seller_id);
                });
            });
        });

        $("#recommendation-link").on('click', function () {
            if (!seller_id) {
                $("#loginModal").modal();
            }
        });

        $("#loginForm").on('submit', function (e) {
            if ($(this).children('#inputSellerId').val() === '') {
                e.preventDefault();
                return false;
            }
            $(this).children('#inputPassword').attr("disabled", "disabled");
            return true;
        });

    })

</script>
</body>
</html>
