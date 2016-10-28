<?php

session_start();
require '../api/hitch_request_controller.php';

if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $new_hr_number = $_GET['new_hr_number'];
    $new_hrs = $_SESSION['new_hrs'];
    $this_hr = $new_hrs[$new_hr_number];
    $hr_pk = $this_hr[17];
    $hr_controller = new Hitch_Request_Controller($hr_pk);
    $success = $hr_controller->AcceptHitchRequest();
    if($success){
        $_SESSION['error'] = "accepted hitch successfully";
    }
    else{
        $_SESSION['error'] = "could not accept";
    }
    header("Location: activity.php");
}
?>
