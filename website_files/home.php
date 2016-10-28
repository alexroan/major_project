<?php

session_start();
require 'menu.php';
require '../api/person.php';
require '../api/journey_controller.php';
unset($_SESSION['search_results']);
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $ps_email = $_SESSION['ps_email'];
    
}

?>
<html>
    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <link href="dist/css/justified-nav.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    </head>
    
    <body>        
        <?php
            include 'home_nav.php';
        ?>
        
        <div class="container">
            <?php
                echo GetMenu("home.php");
            ?>
            <div class="jumbotron">
                <h1>Need to Hitch a Ride?</h1>
                <p>Here are some suggested Journeys for you...</p>
            </div>
            
            <div class="row">
                <?php 
                    $person = new Person($ps_email);
                    $ps_home_1 = $person->Get("ps_home_1");
                    $ps_frequent_destination_1 = $person->Get("ps_frequent_destination_1");
                    if(($ps_home_1 != "") && ($ps_frequent_destination_1 != "")){
                        
                        $journey_controller = new Journey_Controller();
                        $journey_controller->Set("jr_origin", $ps_home_1);
                        $journey_controller->Set("jr_destination", $ps_frequent_destination_1);
                        $journey_controller->Set("search_date_1", date("Y-m-d H:i:s"));
                        $journey_controller->Set("search_date_2", date("Y-m-d H:i:s",strtotime("+8 week")));
                        $search_results = $journey_controller->SearchJourney();
                        $_SESSION['search_results'] = $search_results;
                        $_SESSION['search_origin'] = $ps_home_1;
                        $_SESSION['search_destination'] = $ps_frequent_destination_1;
                        if(count($search_results) > 0){
                            for($i=0; $i<3; $i++){
                                $this_journey = $search_results[$i];
                                $origin = $this_journey['jr_origin'];
                                $destination = $this_journey['jr_destination'];
                                $date = $this_journey['jr_etd'];
                                $spaces_available = $this_journey['jr_spaces_available'];
                                echo "<div class=\"col-lg-4\">
                                        <h3 style=\"text-align: center\">$origin to $destination</h3>
                                        <h4 style=\"text-align: center\">Hitch from $ps_home_1 to $ps_frequent_destination_1</h4>
                                        <p style=\"text-align: center\">Date: $date</p>
                                        <p style=\"text-align: center\">Spaces Available: $spaces_available</p>
                                        <a style=\"padding: 5px\" class=\"btn btn-lg btn-success\" href=\"hitch_view.php?search_result=$i\" role=\"button\">View Journey Details</a>
                                        <a style=\"padding: 5px\" class=\"btn btn-lg btn-success\" href=\"request_hitch.php?search_result=$i\" role=\"button\">Request to Hitch</a>
                                    </div>";
                            }
                        }
                        else{
                            echo "<div class=\"col-lg-4\">
                    
                                </div>
                                <div class=\"col-lg-4\">
                                    <h2 style=\"text-align: center\">No suggestions</h2>
                                </div>
                                <div class=\"col-lg-4\">

                                </div>";
                        }
                    }
                    else{
                        echo "<h2 style=\"text-align: center\">Please update your profile so we can suggest journeys";
                    }
                ?>
                
            </div>            
        </div>
    </body>
</html>