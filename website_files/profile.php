<?php
session_start();
require '../api/person.php';
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
    $ps_email = $_SESSION['ps_email'];
    if($ps_email){
        $person = new Person($ps_email);
        $ps_all = $person->GetAll();
        $ps_first_name = $ps_all['ps_first_name'];
        $ps_last_name = $ps_all['ps_last_name'];
        $_SESSION['ps_all'] = $ps_all;
        $person->CloseConnection();
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
                <?php 
                    foreach($ps_all as $key => $value){
                        $this_key = str_replace("_", " ", str_replace("ps_", "", $key));
                        echo "<div class=\"col-md-4\" style=\"background-color: #eee\">$this_key</div>";
                        if((!$value) || $value == ""){
                            echo "<div class=\"col-md-8\">...</div>";
                        }
                        else{
                            if($this_key == "password"){
                                echo "<div class=\"col-md-8\" style=\"background-color: #eee\">******</div>";
                            }
                            else{
                                echo "<div class=\"col-md-8\" style=\"background-color: #eee\">$value</div>";
                            }
                        }                        
                    }
                ?>  
                <div class="center-block">
                    <p><a class="btn btn-lg btn-success" href="edit_profile.php" role="button">Edit Profile</a></p>
                </div> 
            </div>
                       

        </div>
        
    </body>
    
</html>
