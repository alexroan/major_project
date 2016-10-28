<?php
require '../api/person.php';
session_start();
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $_SESSION['error'] = "Failed to edit profile";
    $ps_email = htmlentities($_POST['email']);
    $ps_first_name = htmlentities($_POST['first_name']);
    $ps_last_name = htmlentities($_POST['last_name']);
    $ps_password = htmlentities($_POST['password']);
    $ps_home_1 = htmlentities($_POST['home_1']);
    $ps_home_2 = htmlentities($_POST['home_2']);
    $ps_frequent_destination_1 = htmlentities($_POST['frequent_destination_1']);
    $ps_frequent_destination_2 = htmlentities($_POST['frequent_destination_2']);
    $ps_frequent_destination_3 = htmlentities($_POST['frequent_destination_3']);
    $ps_current_location = htmlentities($_POST['current_location']);    
    
    $person = new Person($ps_email);
    $person->Set("ps_email", $ps_email);
    $person->Set("ps_first_name", $ps_first_name);
    $person->Set("ps_last_name", $ps_last_name);
    $person->Set("ps_password", $ps_password);
    $person->Set("ps_home_1", $ps_home_1);
    $person->Set("ps_home_2", $ps_home_2);
    $person->Set("ps_frequent_destination_1", $ps_frequent_destination_1);
    $person->Set("ps_frequent_destination_2", $ps_frequent_destination_2);
    $person->Set("ps_frequent_destination_3", $ps_frequent_destination_3);
    $person->Set("ps_current_location", $ps_current_location);
    
    $update = $person->Update();
    $person->CloseConnection();
    if($update){
        $_SESSION['error'] = "Profile successfully edited";
        header("location: profile.php");
    }
    else{
        header("location: profile.php");
    }
}

?>
