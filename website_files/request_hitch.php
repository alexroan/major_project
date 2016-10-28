<?php
require '../api/journey_controller.php';
require '../api/hitch_request_controller.php';
session_start();
$search_result_number = $_GET['search_result'];
$journey = $_SESSION['search_results'][$search_result_number];
$search_controller = new Journey_Controller();
$search_controller->Set("jr_origin", $_SESSION['search_origin']);
$search_controller->Set("jr_destination", $_SESSION['search_destination']);
$geolocate = $search_controller->Geolocate();

if($geolocate){
    if(($journey["jr_origin_lat"] == $search_controller->Get("jr_origin_lat")
            && $journey["jr_origin_lng"] == $search_controller->Get("jr_origin_lng"))
            || $journey["jr_origin"] == $search_controller->Get("jr_origin")){
        $waypoint_1 = "";
    }
    else{
        $waypoint_1 = $search_controller->Get("jr_origin");
    }

    if(($journey["jr_destination_lat"] == $search_controller->Get("jr_destination_lat")
            && $journey["jr_destination_lng"] == $search_controller->Get("jr_destination_lng"))
            || $journey["jr_destination"] == $search_controller->Get("jr_destination")){
        $waypoint_2 = "";
    }
    else{
        $waypoint_2 = $search_controller->Get("jr_destination");
    }
    print_r($journey);
    print_r($journey['jr_pk'].", ".$_SESSION['ps_email'].", ".$waypoint_1.", ".$waypoint_2);
    $hr_controller = new Hitch_Request_Controller();
    $success = $hr_controller->CreateHitchRequest($journey['jr_pk'], $_SESSION['ps_email'], $waypoint_1, $waypoint_2);
    $search_controller->CloseConnection();
    $hr_controller->CloseConnection();
    if($success){
        $_SESSION['error'] = "Hitch Request Sent";
        
    }
    else{
        $_SESSION['error'] = "Could not request Hitch. Please try again later";
    }
}
else{
    $_SESSION['error'] = "Check that the place names you entered exit";
}
header("location: activity.php");

?>
