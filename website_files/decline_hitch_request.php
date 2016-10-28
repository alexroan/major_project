<?php
require '../api/hitch_request_controller.php';
session_start();
$new_hr_number = $_GET['new_hr_number'];
$this_hr = $_SESSION['new_hrs'][$new_hr_number];
$hr_pk = $this_hr[17];
$hr_controller = new Hitch_Request_Controller();
$success = $hr_controller->LoadHitchRequest($hr_pk);

if($success){
    //print_r($hr_controller->Get("hitch_request")->GetAll());
    $success = $hr_controller->DeclineHitchRequest();
    if($success){
        $_SESSION['error'] = "Declined hitch request";
    }
    else{
        $_SESSION['error'] = "Could not decline hitch request, please try again later";
    }
    $journey_number = $_SESSION['journey_number'];
}
else{
    $_SESSION['error'] = "Could not load Hitch Request";
}
$hr_controller->CloseConnection();
header("Location: journey_view.php?journey_number=$journey_number");
?>
