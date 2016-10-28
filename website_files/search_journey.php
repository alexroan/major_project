<?php
session_start();
require 'menu.php';
unset($_SESSION['search_results']);
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    if($_SESSION['error']){
        echo "<script language='javascript'>alert('".$_SESSION['error']."');</script>";
        $_SESSION['error'] = false;
    }
}

?>

<html>
    
    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <link href="dist/css/justified-nav.css" rel="stylesheet">
        <link href="dist/css/signin.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <?php 
            include 'home_nav.php';       
        
            echo "<div class=\"container\">";
            echo GetMenu("search_journey.php");  
        ?>
        
        <form class="form-signin" role="form" action="perform_journey_search.php" method="post">
            <h2 class="form-signin-heading">Search for Journeys</h2> 
            <input name="origin" type="text" class="form-control" placeholder="Origin" required autofocus>
            <input name="destination" type="text" class="form-control" placeholder="Destination" required>
            Between Dates<input name="date_1" type="date" class="form-control" placeholder="Date 1">
            and<input name="date_2" type="date" class="form-control" placeholder="Date 2">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Search</button>
        </form>
        
        <?php
            echo "</div>";
        ?>
    </body>
    
</html>
