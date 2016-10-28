<?php
require '../api/journey_controller.php';
require 'menu.php';
session_start();


if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{     
    
    if(isset($_SESSION['search_results'])){
        $search_results = $_SESSION['search_results'];
    }
    else{
        $origin = filter_var($_POST['origin'], FILTER_SANITIZE_STRING);
        $destination = filter_var($_POST['destination'], FILTER_SANITIZE_STRING);
        $date_1 = filter_var($_POST['date_1'], FILTER_SANITIZE_STRING);
        $date_2 = filter_var($_POST['date_2'], FILTER_SANITIZE_STRING);
        
        $_SESSION['search_origin'] = $origin;
        $_SESSION['search_destination'] = $destination;

        $journey_controller = new Journey_Controller();
        $journey_controller->Set("jr_origin", $origin);
        $journey_controller->Set("jr_destination", $destination);
        $journey_controller->Set("search_date_1", $date_1);
        $journey_controller->Set("search_date_2", $date_2);
        $search_results = $journey_controller->SearchJourney();
        $_SESSION['search_results'] = $search_results;
        $journey_controller->CloseConnection();
    }
    
    
    
}

?>

<html>
    
    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <link href="dist/css/justified-nav.css" rel="stylesheet">
        <link href="dist/css/signin.css" rel="stylesheet">
        <link href="dist/css/grid.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <?php 
            include 'home_nav.php';       
        
            echo "<div class=\"container\">";
            echo GetMenu("search_journey.php");
            
            echo"<a href=search_journey.php><h1>Search Again?</h1></a>
                <h3>Hitch a lift from $origin to $destination on the following journeys...</h3>";
            
            for($i=0; $i<count($search_results); $i++){
                $this_journey = $search_results[$i];
                $origin = $this_journey['jr_origin'];
                $destination = $this_journey['jr_destination'];
                $date = $this_journey['jr_etd'];
                $spaces_available = $this_journey['jr_spaces_available'];
                echo "<div class=\"col-lg-12\" style=\"background-color: #eee\">
                        <div class=\"col-lg-6\" style=\"background-color: #eee\">
                        <h3>$origin to $destination</h3>
                            <p>Date: $date</p>
                            <p>Spaces Available: $spaces_available</p>
                        </div>
                        <div class=\"col-lg-6\" style=\"background-color: #eee\">
                            <a class=\"btn btn-lg btn-success\" href=\"hitch_view.php?search_result=$i\" role=\"button\">View Journey Details</a>
                            <a class=\"btn btn-lg btn-success\" href=\"request_hitch.php?search_result=$i\" role=\"button\">Request to Hitch</a>
                        </div>
                    </div>";                
            }
            
            echo "</div>";
        ?>
    </body>
    
</html>