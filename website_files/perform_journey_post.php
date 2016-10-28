<?php
require '../api/journey_controller.php';
session_start();
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $_SESSION['error'] = "Could not post journey";
    $journey_controller = new Journey_Controller();
    $journey_controller->Set("jr_ps_email", $_SESSION['ps_email']);
    $journey_controller->Set("jr_origin", $_SESSION['jr_origin']);
    $journey_controller->Set("jr_destination", $_SESSION['jr_destination']);
    $journey_controller->Set("jr_etd", $_SESSION['jr_etd']);
    $journey_controller->Set("jr_eta", $_SESSION['jr_eta']);
    $journey_controller->Set("jr_total_spaces", $_SESSION['jr_spaces_available']);
    $journey_controller->Set("jr_spaces_available", $_SESSION['jr_spaces_available']);
    $journey_controller->Set("jr_description", $_SESSION['jr_description']);
    
    $create = $journey_controller->CreateJourney();
    $journey_controller->CloseConnection();
    if($create){
        $_SESSION['error'] = "Posted journey successfully!";
        header("location: activity.php");
    }
    else{
        header("location: post_journey.php");
    }
}

?>
