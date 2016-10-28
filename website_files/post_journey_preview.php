<?php

session_start();
require 'menu.php';
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $_SESSION['jr_origin'] = htmlentities($_POST['origin']);
    $_SESSION['jr_destination'] = htmlentities($_POST['destination']);    
    $_SESSION['jr_etd'] = htmlentities($_POST['etd'])." ".htmlentities($_POST['etd_time']);
    $_SESSION['jr_eta'] = htmlentities($_POST['eta'])." ".htmlentities($_POST['eta_time']);
    $_SESSION['jr_spaces_available'] = htmlentities($_POST['spaces_available']);
    $_SESSION['jr_description'] = htmlentities($_POST['description']);
    $jr_origin = $_SESSION['jr_origin'];
    $jr_destination = $_SESSION['jr_destination'];
    $jr_etd = $_SESSION['jr_etd'];
    $jr_eta = $_SESSION['jr_eta'];
    $jr_spaces_available = $_SESSION['jr_spaces_available'];
    $jr_description = $_SESSION['jr_description'];
    $javascript = "<script>

            var rendererOptions = {
                draggable: false
            };
	    
            var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
            var directionsService = new google.maps.DirectionsService();
            var map;

            function initialize() {
                var mapOptions = {
                    zoom: 5,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
                directionsDisplay.setMap(map);
                calcRoute();
            }

            function calcRoute() {
                var request = {
                    origin: '$jr_origin',
                    destination: '$jr_destination',
                    travelMode: google.maps.DirectionsTravelMode.DRIVING,
					provideRouteAlternatives: false
                };
                directionsService.route(request, function(response, status) {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                    }
                    else{
                        alert(\"One or both of the locations you entered cannot be found. Please try again.\");
                        window.location.assign(\"post_journey.php\");
                    }
                });
            }

            google.maps.event.addDomListener(window, 'load', initialize);

        </script>";    
}
?>

<html>
    
    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <link href="dist/css/justified-nav.css" rel="stylesheet">
        <!--<link href="dist/css/grid.css" rel="stylesheet">-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
        <script src="dist/js/bootstrap.min.js"></script>
        <?php
            echo $javascript;
        ?>
    </head>
    
    <body>
        <?php
            include 'home_nav.php';
            
            echo "<div class=\"container\">";
            echo GetMenu("post_journey.php");  
        ?>
        
        <div class="row">
            <div class="col-lg-12">
                <?php echo "<h1>Your Proposed Journey from $jr_origin to $jr_destination</h1>"; ?>
            </div>
        </div> 
        <div class="row">
            <div class="col-lg-6">
                <div class="col-lg-12">
                    <h2>Journey Route</h2>
                </div>
                <div class="col-lg-12">
                    <div id="map-canvas" style="float:left;width:100%; height:50%"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="col-lg-12">
                    <?php
                        echo "<h2>Journey Details</h2>
                            <p>Origin: $jr_origin</p>
                            <p>Destination: $jr_destination</p>
                            <p>ETD: $jr_etd</p>
                            <p>ETA: $jr_eta</p>
                            <p>Spaces Available: $jr_spaces_available</p>
                            <p>Description: $jr_description</p>
                            <h2>Post Journey?</h2>";  
                    ?>
                                      
                    <a class="btn btn-lg btn-success" href="perform_journey_post.php" role="button">Accept Journey?</a>
                    <a class="btn btn-lg btn-success" href="post_journey.php" role="button">Try Again</a>
                </div>
            </div>
        </div>
        
    </body>
    
</html>
