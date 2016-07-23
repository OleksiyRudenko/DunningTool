<?php
include_once('app/preHTML.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//UK" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>Dunning App</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap Core CSS -->
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <!-- link href="css/general.css" rel="stylesheet" -->

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- script src="http://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script -->

    <script src="js/datex.js"></script>

</head>
<body style="position:relative;" data-spy="scroll" data-target=".navbar" data-offset="50">
<!-- http://www.w3schools.com/bootstrap/bootstrap_scrollspy.asp -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Dunning Tool</a>
        </div>
        <div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <?php
                    // build nav menu
                    foreach (MODULE::$path2mod as $p=>$m)
                        print '<li><a href="'
                            .( $m==MODULE::$current
                            ? '#section-'.$m
                            : $p )
                            .'">'.MODULE::$setting[$m]['navmenu'].'</a></li>';
                    ?>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div id="section-<?=MODULE::$current?>" class="container" style="padding-top: 50px;">
    <div class="col-lg-10 col-lg-offset-1">
        <?php
            include_once('app/main.php');
        ?>
    </div>
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script src="js/bootstrap-datepicker.js"></script>

<!-- Custom Theme JavaScript -->
<script>
    // Closes the sidebar menu
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
    // Opens the sidebar menu
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
    // Scrolls to the selected menu item on the page
    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
    //#to-top button appears after scrolling
    var fixed = false;
    $(document).scroll(function() {
        if ($(this).scrollTop() > 250) {
            if (!fixed) {
                fixed = true;
                // $('#to-top').css({position:'fixed', display:'block'});
                $('#to-top').show("slow", function() {
                    $('#to-top').css({
                        position: 'fixed',
                        display: 'block'
                    });
                });
            }
        } else {
            if (fixed) {
                fixed = false;
                $('#to-top').hide("slow", function() {
                    $('#to-top').css({
                        display: 'none'
                    });
                });
            }
        }
    });

    // bootstrap-datepicker
    // https://github.com/eternicode/bootstrap-datepicker
    // https://bootstrap-datepicker.readthedocs.io/en/stable/options.html
    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $.fn.datepicker.defaults.weekStart = 1;
    $.fn.datepicker.defaults.autoclose = true;
    $.fn.datepicker.defaults.daysOfWeekHighlighted = [0,6];
    $('.datepicker').datepicker(); // .on('changeDate', function(ev){
        // $('#'+ev.target.id).datepicker('hide');
        // $(this).datepicker('hide');
        /* var idx = $(this).attr('data-datebizidx');
        setDOWvalue($(this).val(),'newValue'+idx); */
    //});
    /* .blur(function(ev){
        $(this).datepicker('hide');
    }); */

    /* $('.DateBizNewDate').change(function (ev) {
        var idx = $(this).attr('data-datebizidx');
        setDOWvalue($(this).val(),'newValue'+idx);
        });

        datepicker overrides element's onchange
        datepicker doesn't hide on another input field focus

        */


    // Disable Google Maps scrolling
    // See http://stackoverflow.com/a/25904582/1607849
    // Disable scroll zooming and bind back the click event
    /* var onMapMouseleaveHandler = function(event) {
        var that = $(this);
        that.on('click', onMapClickHandler);
        that.off('mouseleave', onMapMouseleaveHandler);
        that.find('iframe').css("pointer-events", "none");
    }
    var onMapClickHandler = function(event) {
        var that = $(this);
        // Disable the click handler until the user leaves the map area
        that.off('click', onMapClickHandler);
        // Enable scrolling zoom
        that.find('iframe').css("pointer-events", "auto");
        // Handle the mouse leave event
        that.on('mouseleave', onMapMouseleaveHandler);
    }
    // Enable map zooming with mouse scroll when the user clicks the map
    $('.map').on('click', onMapClickHandler); */
</script>
<!-- script src="js/postcontact.js"></script>
<script src="js/jquery.ready.js"></script -->

</body>
</html>