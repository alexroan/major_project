<?php

session_start();
require 'menu.php';

if(!$_SESSION['ps_email']){
    header("Location: index.php");
}
else{
    $ps_all = $_SESSION['ps_all'];
    $ps_first_name = $ps_all['ps_first_name'];
    $ps_last_name = $ps_all['ps_last_name'];
}

?>

<html>

    <head>
        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Template custom style-->
        <link href="dist/css/jumbotron.css" rel="stylesheet">
        <link href="dist/css/justified-nav.css" rel="stylesheet">
        <link href="dist/css/grid.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <?php
            include 'home_nav.php';
        ?>        
        <div class="container">
            <?php
                echo GetMenu("profile.php");
            ?>
            <div class="col-lg-12" style="background-color: white; border: 0;">
                <h2><?php echo $ps_first_name." ".$ps_last_name; ?></h2>
                <form class="form-signin" id="edit_profile_form" method="post" action="perform_edit_profile.php">
                    <?php 
                        
                        foreach($ps_all as $key => $value){
                            $input_box_name = str_replace("ps_", "", $key);
                            $this_key = str_replace("_", " ", str_replace("ps_", "", $key));
                            echo "<div class=\"col-md-4\" style=\"background-color: #eee\">$this_key</div>";
                            if($this_key == "password"){
                                echo "<div class=\"col-md-8\" style=\"background-color: #eee\"><input name=\"$input_box_name\" type=\"password\" class=\"form-control\" value=\"$value\" required></div>";
                            }
                            else{
                                echo "<div class=\"col-md-8\" style=\"background-color: #eee\"><input name=\"$input_box_name\" type=\"text\" class=\"form-control\" value=\"$value\" required></div>";
                            }
                                                   
                        }
                        
                    ?>  
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Submit Profile</button>
                </form>
            </div>
                       
        </div>
    </body>
    
</html>