<?php
session_start();
require 'google_map_generator.php';
require '../api/hitch_request_controller.php';
require 'menu.php';

if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    
    if($_SESSION['error']){
        echo "<script language='javascript'>alert('".$_SESSION['error']."');</script>";
        $_SESSION['error'] = false;
    }
    
    
    if(isset($_GET['hitch_number'])){
        $hitch_number = $_GET['hitch_number'];
        $hitch_request = $_SESSION['my_hrs'][$hitch_number];
        $jr_pk = $hitch_request[0];
        $hr_controller = new Hitch_Request_Controller();
        $hitch_requests = $hr_controller->GetHitchRequestsForJourney($jr_pk);
        $jr_origin = $hitch_request[2];
        $jr_destination = $hitch_request[3];
        $pickup = $jr_origin;
        $dropoff = $jr_destination;
        if($hitch_request[23] != ""){
            $pickup = $hitch_request[23];
        }
        if($hitch_request[24] != ""){
            $dropoff = $hitch_request[24];
        }
        if($hitch_requests){
            $waypoints = Array();
            $accepted_hrs = Array();
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
            }
            if(!in_array($pickup, $waypoints, true)){
                array_push($waypoints, $pickup);
            }  
            if(!in_array($dropoff, $waypoints, true)){
                array_push($waypoints, $dropoff);
            } 
            $jr_controller = $hr_controller->Get("journey_controller");
            $jr_controller->LoadJourney($jr_pk);
            $journey = $jr_controller->GetJourneyDataAll();
            $javascript = GetMap($journey, $waypoints);
        }
        
        $output = "";
        if($hitch_request[22] == "0"){
            $response = "REJECTED";
        }
        else if ($hitch_request[22] == "1"){
            $response = "ACCEPTED";
        }
        else{
            $response = "NO RESPONSE YET";
        }
        $output = $output."<div class=\"col-lg-12\"><h2>$response</h2></div>";
        if(isset($accepted_hrs)){
            if(count($accepted_hrs) > 0){
                $output = $output."<div class=\"col-lg-12\">
                        <h2>Hitchers</h2>";
                for($i=0; $i<count($accepted_hrs); $i++){
                    $output = $output."<div class=\"col-lg-12 row\" style=\"background-color: #eee; border-width: 1px;\">";
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
                    $output = $output."<div class=\"col-lg-6\">User:
                        </div>
                        <div class=\"col-lg-6\">".$this_accepted_hr[19]."
                        </div>
                        <div class=\"col-lg-6\">Picking up at:                        
                        </div>
                        <div class=\"col-lg-6\">$pickup                       
                        </div>
                        <div class=\"col-lg-6\">Dropping off at:                        
                        </div>
                        <div class=\"col-lg-6\">$dropoff                        
                        </div>
                        </div>";
                }
                $output = $output."</div>";
            }
        }
        $hr_controller->CloseConnection();
    }
    else if(isset($_GET['search_result'])){
        $search_results = $_SESSION['search_results'];
        $result_number = $_GET['search_result'];
        $this_journey = $search_results[$result_number];
        $jr_pk = $this_journey['jr_pk'];
        $jr_origin = $this_journey['jr_origin'];
        $jr_destination = $this_journey['jr_destination'];
        $waypoints = Array();
        
        $pickup = $_SESSION['search_origin'];
        $dropoff = $_SESSION['search_destination'];
        if(!in_array($pickup, $waypoints, true)){
            array_push($waypoints, $pickup);
        } 
        if(!in_array($dropoff, $waypoints, true)){
            array_push($waypoints, $dropoff);
        }
        $jr_controller = new Journey_Controller();
        $jr_controller->LoadJourney($jr_pk);
        $journey = $jr_controller->GetJourneyDataAll();
        $javascript = GetMap($journey, $waypoints);
        $output = "<div class=\"col-lg-12\">
        <a class=\"btn btn-lg btn-success\" href=\"request_hitch.php?search_result=$result_number\" role=\"button\">Request Hitch</a>
        <a class=\"btn btn-lg btn-success\" href=\"perform_journey_search.php\" role=\"button\">Find Another</a>
        </div>";
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
            <div class="row">
                <div class="col-lg-12">
                    <?php echo "<h1>Your Hitch Request from $pickup to $dropoff</h1>"; ?>
                </div>
            </div>   
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
                    echo $output;
                ?>
            </div>
        </div>
        
    </body>
    
</html>