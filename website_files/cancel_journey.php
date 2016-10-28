<?php
require '../api/journey_controller.php';
session_start();
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $_SESSION['error'] = "Failed to cancel journey";
    $journey = $_SESSION['my_journeys'][$_SESSION['journey_number']];
    $jr_pk = $journey['jr_pk'];
    $jr_controller =new Journey_Controller();
    $load = $jr_controller->LoadJourney($jr_pk);
    if($load){
        $cancel = $jr_controller->CancelJourney();
        if($cancel){
            $_SESSION['error'] = "Journey cancelled";
        }
    }
    $journey_number = $_SESSION['journey_number'];
    $jr_controller->CloseConnection();
    header("Location: activity.php");
}

?>
