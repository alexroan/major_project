<?php
session_start();
require 'menu.php';
unset($_SESSION['search_results']);
if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    
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
            echo GetMenu("post_journey.php");  
        ?>
        
        <div class="col-lg-12">
            <form class="form-signin" id="post_journey_form" method="post" action="post_journey_preview.php">
                <h2 class="form-signin-heading">Post a Journey</h2>
                <input name="origin" type ="text" class="form-control" placeholder="Origin" required>
                <input name="destination" type ="text" class="form-control" placeholder="Destination" required>
                <h4>Departure date</h4>
                <input name="etd" type ="date" class="form-control" placeholder="etd" required>
                <input name="etd_time" type ="time" class="form-control" placeholder="Departure Time (24hr)" required>
                <h4>Expected arrival date</h4>
                <input name="eta" type ="date" class="form-control" placeholder="etd" required>
                <input name="eta_time" type ="time" class="form-control" placeholder="Arrival Time (24hr)" required>
                <input name="spaces_available" type ="text" class="form-control" placeholder="Spaces Available" required>
                <textarea name="description" style="resize: none" rows="4" form="post_journey_form" class="form-control">Description</textarea>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Post Journey?</button>
            </form>
            
        </div>
        
        <?php
            echo "</div>";
        ?>
        
    </body>
    
    
</html>
    
