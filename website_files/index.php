<?php

    session_start();
    if($_SESSION['error']){
        echo "<script language='javascript'>alert('".$_SESSION['error']."');</script>";
        $_SESSION['error'] = false;
    }
    if($_SESSION['ps_email']){
        header("Location: home.php");
    }

?>
<html>
    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    
    </head>    
    
    <body>
        <?php
            include 'index_nav.php';
        ?>
        
        <div class="jumbotron">
            <div class="container">
                <h1>Welcome!</h1>
                <p>Welcome to Hitch A Ride!</p>
                <form class="form-signin" id="register-form" action="register.php" method="post" role="form">
                    <div style="width: 49%; float: left">
                        <div class="form-group">
                            <input name="first_name" type="text" placeholder="First Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <input name="last_name" type="text" placeholder="Last Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <input name="email" type="text" placeholder="Email" class="form-control">
                        </div>
                    </div>
                    <div style="width: 49%; float: right">
                        <div class="form-group">
                            <input name="password" type="password" placeholder="Password" class="form-control">
                        </div>
                        <div class="form-group">
                            <input name="confirm_password" type="password" placeholder="Confirm Password" class="form-control">
                        </div>
                        <button type="submit" style="width: 100%" class="btn btn-success btn-primary">Register</button>
                    </div>                       
                </form>    
            </div>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2>Post Your Journeys!</h2>
                    <p>Post journeys that you're taking if you have any spare seats.</p>
                </div>
                <div class="col-md-4">
                    <h2>Pick up Hitchers!</h2>
                    <p>Pick up hitchers who want to travel with you to save on fuel costs.</p>
                </div>
                <div class="col-md-4">
                    <h2>Hitch Rides!</h2>
                    <p>Hitch other people's rides to travel on the cheap.</p>
                </div>
            </div>
        </div>  
    </body>
</html>