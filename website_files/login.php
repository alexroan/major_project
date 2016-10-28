<?php
session_start();

require '../api/person.php';

$email = htmlentities(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL));
$password = htmlentities(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

$person = new Person();
$load = $person->Load($email);
$person->CloseConnection();
if(!$load){
    $_SESSION['error'] = "email and password did not match our records";
    header("Location: index.php");
}
else{
    if($person->Get("ps_password") == $password){
        $_SESSION['ps_email'] = $email;
        header("Location: home.php");
    }
    else{
        $_SESSION['error'] = "email and password did not match our records";
        header("Location: index.php");
    }
}



?>
