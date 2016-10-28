<?php

session_start();
session_unset();
//$_SESSION['ps_email'] = false;
header("Location: index.php");

?>
