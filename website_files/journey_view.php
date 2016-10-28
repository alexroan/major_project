<?php

session_start();
require 'menu.php';
require 'google_map_generator.php';
require '../api/hitch_request_controller.php';

if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    if($_SESSION['error']){
        echo "<script language='javascript'>alert('".$_SESSION['error']."');</script>";
        $_SESSION['error'] = false;
    }
    $journey_number = $_GET['journey_number'];
    $_SESSION['journey_number'] = $journey_number;
    if(isset($journey_number)){
        $journey = $_SESSION['my_journeys'][$journey_number];
        $hr_controller = new Hitch_Request_Controller();
        $hitch_requests = $hr_controller->GetHitchRequestsForJourney($journey['jr_pk']);
        $jr_origin = $journey["jr_origin"];
        $jr_destination = $journey["jr_destination"];
        if($hitch_requests){
            $waypoints = Array();
            $accepted_hrs = Array();
            $new_hrs = Array();
            for($i=0; $i<count($hitch_requests); $i++){
                $this_hitch_request = $hitch_requests[$i];
                $waypoint_1 = $this_hitch_request[23];
                $waypoint_2 = $this_hitch_request[24];
                if($this_hitch_request[22] == 1){
                    array_push($accepted_hrs, $this_hitch_request);
                    if($waypoint_1 != ""){
                        if(!in_array($waypoint_1, $waypoints, true)){
                            array_push($waypoints, $waypoint_1);
                        }                        
                    }
                    if($waypoint_2 != ""){
                        if(!in_array($waypoint_2, $waypoints, true)){
                            array_push($waypoints, $waypoint_2);
                        } 
                    }
                }
                if($this_hitch_request[21] == ""){
                    array_push($new_hrs, $this_hitch_request);
                }
            }
            $_SESSION['new_hrs'] = $new_hrs;
        }
        $javascript = GetMap($journey, $waypoints);
        $hr_controller->CloseConnection();
    }
    else{
        print_r($journey_number);
        //header("Location: activity.php");
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
            echo GetMenu("activity.php");  
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php echo "<h1>Your Journey from $jr_origin to $jr_destination</h1>"; ?>
                </div>
            </div>    
            <div class="row">
                <div class="col-lg-6">                    
                    <div class="col-lg-12">
                        <h2>Journey Details</h2>                        
                    </div>
                    <div class="col-lg-12">
                        <div id="map-canvas" style="float:left;width:100%; height:50%"></div>
                    </div>
                    <div class="col-lg-12">
                        <?php
                            foreach($journey as $key => $value){
                                if($value == ""){
                                    $value = "_";
                                }
                                echo "<div class=\"col-lg-6\">".str_replace("_", " ", str_replace("jr", "", $key))."                        
                                </div>
                                <div class=\"col-lg-6\">".$value."                        
                                </div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="col-lg-6">                    
                    <?php
                        if(isset($new_hrs)){
                            if(count($new_hrs) > 0){     
                                echo "<div class=\"col-lg-12\">
                                        <h2>New Hitch Requests</h2>";
                                for($i=0; $i<count($new_hrs); $i++){
                                    $user = $new_hrs[$i][19];
                                    $pickup = $new_hrs[$i][23];
                                    if($pickup == ""){
                                        $pickup = $new_hrs[$i][2];
                                    }
                                    $dropoff = $new_hrs[$i][24];
                                    if($dropoff == ""){
                                        $dropoff = $new_hrs[$i][3];
                                    }
                                    echo "<div class=\"col-lg-12\">
                                            <p>User: $user</p>
                                            <p>Hitching from: $pickup</p>
                                            <p>Hitching to: $dropoff</p>
                                            <a class=\"btn btn-lg btn-success\" href=\"accept_hitch_request.php?new_hr_number=$i\" role=\"button\">Accept Hitch?</a>
                                            <a class=\"btn btn-lg btn-success\" href=\"decline_hitch_request.php?new_hr_number=$i\" role=\"button\">Decline Hitch?</a>
                                        </div>";
                                }
                                echo "</div>";
                            }
                        }
                        unset($new_hrs);
                        if(isset($accepted_hrs)){
                            if(count($accepted_hrs) > 0){
                                echo "<div class=\"col-lg-12\">
                                        <h2>Hitchers</h2>";
                                for($i=0; $i<count($accepted_hrs); $i++){
                                    echo "<div class=\"col-lg-12 row\" style=\"background-color: #eee; border-width: 1px; border-style:solid\">";
                                    $this_accepted_hr = $accepted_hrs[$i];                                    
                                    if($this_accepted_hr[23] == ""){
                                        $pickup = $jr_origin;
                                    }
                                    else{
                                        $pickup = $this_accepted_hr[23];
                                    }
                                    if($this_accepted_hr[24] == ""){
                                        $dropoff = $jr_destination;
                                    }
                                    else{
                                        $dropoff = $this_accepted_hr[24];
                                    }
                                    echo "<div class=\"col-lg-6\">User:
                                        </div>
                                        <div class=\"col-lg-6\">".$this_accepted_hr[19]."
                                        </div>
                                        <div class=\"col-lg-6\">Picking up at:                        
                                        </div>
                                        <div class=\"col-lg-6\">$pickup                       
                                        </div>";
                                    echo "<div class=\"col-lg-6\">Dropping off at:                        
                                        </div>
                                        <div class=\"col-lg-6\">$dropoff                        
                                        </div>";
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        }
                    ?>
                    <div class="col-lg-12">
                        <h3>Cancelling Journey</h3>
                        <a class="btn btn-lg btn-success" href="cancel_journey.php" role="button">Cancel Journey</a>
                    </div>
                </div>
            </div>        
        </div>
    </body>
    
</html>
