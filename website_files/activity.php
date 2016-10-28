<?php
session_start();
require '../api/journey_controller.php';
require '../api/hitch_request_controller.php';
require 'menu.php';
unset($_SESSION['search_results']);
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    if($_SESSION['error']){
        echo "<script language='javascript'>alert('".$_SESSION['error']."');</script>";
        $_SESSION['error'] = false;
    }
    $journey_controller = new Journey_Controller();
    $my_journeys = $journey_controller->GetMyJourneys($_SESSION['ps_email']);
    $hr_controller = new Hitch_Request_Controller();
    $my_hrs = $hr_controller->GetMyHitchRequests($_SESSION['ps_email']);
    $new_hrs = $hr_controller->GetNewRequests($_SESSION['ps_email']);
    //print_r($new_hrs);
    
    $_SESSION['my_hrs'] = $my_hrs;
    $_SESSION['my_journeys'] = $my_journeys;
    $max_rows = count($my_journeys);
    if(count($my_hrs) > $max_rows){
        $max_rows = count($my_hrs);
    }
    $output = "<div class=\"col-lg-12\"  style=\"background-color: white; border: 0;\"><h2>My Activity</h2>";
    $output = $output."<div class=\"col-lg-6\" style=\"background-color: white; border-width: 0;\"><h3>My Journeys</h3>";                    
    for($i=0; $i<$max_rows; $i++){
        $this_journey = $my_journeys[$i];
        if($this_journey){
            $journey_hrs = 0;
            for($j=0; $j<count($new_hrs); $j++){
                if($this_journey['jr_pk'] == $new_hrs[$j][0]){
                    $journey_hrs++;
                }
            }
            $origin = $this_journey['jr_origin'];
            $destination = $this_journey['jr_destination'];
            $date = $this_journey['jr_etd'];
            $spaces_available = $this_journey['jr_spaces_available'];
            if($journey_hrs > 0){
                $output = $output." <a href=\"journey_view.php?journey_number=$i\"><div class=\"col-lg-12\" style=\"border-color:red\">
                    <h3>$origin to $destination</h3>
                        <p>Date: $date</p>
                        <p>Spaces Available: $spaces_available</p>
                        <p>$journey_hrs NEW HITCH REQUESTS!</p>
                </div></a>";
            }
            else{
                $output = $output." <a href=\"journey_view.php?journey_number=$i\"><div class=\"col-lg-12\" style=\"background-color: #eee\">
                    <h3>$origin to $destination</h3>
                        <p>Date: $date</p>
                        <p>Spaces Available: $spaces_available</p>
                </div></a>";
            }
        }
    }
    $output = $output."</div><div class=\"col-lg-6\"  style=\"background-color: white; border-width: 0;\"><h3>My Hitches</h3>";     
    for($i=0; $i<$max_rows; $i++){
        $this_hr = $my_hrs[$i];
        if($this_hr){
            $origin = $this_hr[2];
            $destination = $this_hr[3];
            $driver_email = $this_hr[1];
            $date = $this_hr[4];
            $hitch_from = $origin;
            if($this_hr[23] != ""){
                $hitch_from = $this_hr[23];
            }
            $hitch_to = $destination;
            if($this_hr[24] != ""){
                $hitch_to = $this_hr[24];
            }
            $response = "NO RESPONSE YET";
            $border_colour = null;
            $background_colour = null;
            if($this_hr[22] == "1"){
                $response = "ACCEPTED";
                $border_colour = "green";
                $background_colour = "#eee";
            }
            else if($this_hr[22] == "0"){
                $response = "REJECTED";
                $border_colour = "red";
            }
            $output = $output."<a href=\"hitch_view.php?hitch_number=$i\"><div class=\"col-lg-12\" style=\"background-color: $background_colour; border-color: $border_colour\">
                    <h3>$response</h3>
                    <h4>$origin to $destination</h4>                    
                    <p>Hitching From: $hitch_from</p>
                    <p>Hitching To: $hitch_to</p>
                    <p>Driver: $driver_email</p>
                    <p>Date: $date</p>
                    
            </div></a>";
        }
    }
    $output = $output."</div></div>";
    $journey_controller->CloseConnection();
    $hr_controller->CloseConnection();
}

?>


<html>
    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <link href="dist/css/justified-nav.css" rel="stylesheet">
        <link href="dist/css/grid.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <?php 
            include 'home_nav.php';       
        
            echo "<div class=\"container\">";
            echo GetMenu("activity.php");

            //Display Journeys and Hitches                    
            echo $output;
            echo "</div>";
        ?>            
    </body>
</html>