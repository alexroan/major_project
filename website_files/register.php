<?php
session_start();
require '../api/person.php';

$first_name = htmlentities(filter_var($_POST['first_name'], FILTER_SANITIZE_STRING));
$last_name = htmlentities(filter_var($_POST['last_name'], FILTER_SANITIZE_STRING));
$email = htmlentities(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL));
$password = htmlentities(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

$person = new Person();
$person->Set("ps_first_name", $first_name);
$person->Set("ps_last_name", $last_name);
$person->Set("ps_email", $email);
$person->Set("ps_password", $password);

$create_request = $person->Create();
$person->CloseConnection();
if($create_request){
    $_SESSION['ps_email'] = $email;
    header("Location: home.php");
}else{
    $_SESSION['error'] = "could not register new person";
    header("Location: index.php");
}

