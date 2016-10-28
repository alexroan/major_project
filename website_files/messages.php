<?php
require '../api/message_controller.php';
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
    $m_controller = new Message_Controller();
    $message_load = $m_controller->LoadMyMessages($_SESSION['ps_email'], "i");
    if($message_load){
        $my_messages = $m_controller->GetMessages();
    }
    $sent_items_load = $m_controller->LoadMyMessages($_SESSION['ps_email'], "s");
    if($sent_items_load){
        $sent_messages = $m_controller->GetMessages();
    }
    
    $m_controller->CloseConnection();
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
        <script>
            $(document).ready(function(){
                $("#sent_button").click(function(){
                    $("#inbox_div").hide();
                    $("#inbox_title").hide();
                    $("#new_message_div").hide();
                    $("#new_message_title").hide();
                    $("#sent_div").show();
                    $("#sent_title").show();
                    $("#sent_button").css("background-color", "#eee");
                    $("#inbox_button").css("background-color", "white");
                    $("#new_message_button").css("background-color", "white");
                });
                $("#inbox_button").click(function(){
                    $("#sent_div").hide();
                    $("#sent_title").hide();
                    $("#new_message_div").hide();
                    $("#new_message_title").hide();
                    $("#inbox_div").show();
                    $("#inbox_title").show();
                    $("#sent_button").css("background-color", "white");
                    $("#inbox_button").css("background-color", "#eee");
                    $("#new_message_button").css("background-color", "white");
                });
                $("#new_message_button").click(function(){
                    $("#sent_div").hide();
                    $("#sent_title").hide();
                    $("#inbox_div").hide();
                    $("#inbox_title").hide();
                    $("#new_message_div").show();
                    $("#new_message_title").show();
                    $("#sent_button").css("background-color", "white");
                    $("#inbox_button").css("background-color", "white");
                    $("#new_message_button").css("background-color", "#eee");
                });
                
                <?php
                    $javascript = "";
                    for($i=0; $i<count($my_messages); $i++){
                        $javascript = $javascript."$(\"#message_button_$i\").click(function(){\n";
                        for($j=0; $j<count($my_messages); $j++){
                            if($j != $i){
                                $javascript = $javascript."$(\"#message_div_$j\").hide();
                                        $(\"#message_li_$j\").css(\"background-color\", \"white\");";
                            }
                        }
                        $javascript = $javascript."$(\"#message_div_$i\").show();
                            $(\"#message_li_$i\").css(\"background-color\", \"#eee\");
                            });";
                    }
                    
                    for($i=0; $i<count($sent_messages); $i++){
                        $javascript = $javascript."$(\"#sent_button_$i\").click(function(){\n";
                        for($j=0; $j<count($sent_messages); $j++){
                            if($j != $i){
                                $javascript = $javascript."$(\"#sent_div_$j\").hide();
                                        $(\"#sent_li_$j\").css(\"background-color\", \"white\");";
                            }
                        }
                        $javascript = $javascript."$(\"#sent_div_$i\").show();
                            $(\"#sent_li_$i\").css(\"background-color\", \"#eee\");
                            });";
                    }
                    echo $javascript;
                ?>
                
            });
        </script>
    </head>
    <body>
        <?php
            include 'home_nav.php';
        ?>
        <div class="container" style="background-color: white; border: 0;">
            <?php 
                echo GetMenu("messages.php");
            ?>
            <div class="col-lg-12" style="background-color: white; border: 0;">
                <h2 class="col-lg-2" style="padding:0; background-color: white; border: 0;">Messages</h2>
                <h2 id="new_message_title" class="col-lg-10" style="display:none; padding:0; background-color: white; border: 0;">New Message</h2>
                <h2 id="inbox_title" class="col-lg-10" style="padding:0; background-color: white; border: 0;">Inbox</h2>
                <h2 id="sent_title" class="col-lg-10" style="display: none; padding:0; background-color: white; border: 0;">Sent Messages</h2>
                <div class="col-lg-2 sidebar" style="background-color: white; border: 0;">
                    <ul class="nav nav-sidebar">
                        <li id="new_message_li" ><a id="new_message_button">New Message</a></li>
                        <li style="background-color: #eee" id="inbox_li" ><a id="inbox_button">Inbox</a></li>
                        <li id="sent_li" ><a id="sent_button">Sent</a></li>
                    </ul>
                </div>
                <div class="col-lg-10" id="new_message_div" style="display: none; background-color: white; border: 0;">
                    <form class="form-signin" id="send_message_form" method="post" action="send_message.php">
                        <input name="send_to" type="text" class="form-control" placeholder="Send To" required>
                        <input name="subject" type="text" class="form-control" placeholder="Subject">
                        <textarea name="message_content" style="resize:none" rows="20" form="send_message_form" class="form-control">Message Body</textarea>
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Send Message</button>
                    </form>
                </div>
                <div class="col-lg-10" id="inbox_div" style="background-color: white; border: 0;">
                    <?php
                        if(count($my_messages) > 0){
                            echo "<div class=\"col-lg-3 sidebar\" style=\"background-color: white; border: 0; padding: 0;\">
                                <ul class=\"nav nav-sidebar\">";
                            for($i=0; $i<count($my_messages); $i++){
                                $subject = $my_messages[$i]['ms_title'];
                                $body = $my_messages[$i]['ms_body'];
                                $ms_from = $my_messages[$i]['ms_ps_sender'];
                                if($i == 0){
                                    echo "<li id=\"message_li_$i\" style=\"background-color: #eee\" ><a id=\"message_button_$i\"><h4 style=\"padding-top: 0; margin-top: 0;\">$subject</h4><h5>From: $ms_from</h5></a></li>";
                                }
                                else{
                                    echo "<li id=\"message_li_$i\" ><a id=\"message_button_$i\"><h4 style=\"padding-top: 0; margin-top: 0;\">$subject</h4><h5>From: $ms_from</h5></a></li>";
                                }
                            }
                            echo "</ul></div>";
                            for($i=0; $i<count($my_messages); $i++){
                                $body = $my_messages[$i]['ms_body'];
                                if($i == 0){
                                    echo "<div class=\"col-lg-9\" id=\"message_div_$i\" style=\"background-color: white; border: 0;\">";
                                }
                                else{
                                    echo "<div class=\"col-lg-9\" id=\"message_div_$i\" style=\"background-color: white; border: 0; display:none;\">";
                                }
                                
                                echo "<form id=\"message_form_$i\">
                                    <!--<button class=\"btn btn-sm btn-primary form-inline\" id=\"reply_message_$i\" type=\"submit\">Reply</button>
                                    <button class=\"btn btn-sm btn-primary form-inline\" id=\"delete_message_$i\" type=\"submit\">Delete</button>
                                    --><textarea form=\"message_ name=\"message_content_$i\" style=\"resize:none\" rows=\"10\" class=\"form-control\" readonly>$body</textarea>
                                        </form>
                                        </div>";
                            }
                        }
                        else{
                            echo "<h4>No Messages</h4>";
                        }
                    ?>
                </div>
                <div class="col-lg-10" id="sent_div" style="display: none; background-color: white; border: 0;">
                    <?php
                        if(count($sent_messages) > 0){
                            echo "<div class=\"col-lg-3 sidebar\" style=\"background-color: white; border: 0; padding: 0;\">
                                <ul class=\"nav nav-sidebar\">";
                            for($i=0; $i<count($sent_messages); $i++){
                                $subject = $sent_messages[$i]['ms_title'];
                                $body = $sent_messages[$i]['ms_body'];
                                $ms_to = $sent_messages[$i]['ms_ps_receiver'];
                                if($i == 0){
                                    echo "<li id=\"sent_li_$i\" style=\"background-color: #eee\" ><a id=\"sent_button_$i\"><h4 style=\"padding-top: 0; margin-top: 0;\">$subject</h4><h5>To: $ms_to</h5></a></li>";
                                }
                                else{
                                    echo "<li id=\"sent_li_$i\" ><a id=\"sent_button_$i\"><h4 style=\"padding-top: 0; margin-top: 0;\">$subject</h4><h5>To: $ms_to</h5></a></li>";
                                }
                            }
                            echo "</ul></div>";
                            for($i=0; $i<count($sent_messages); $i++){
                                $body = $sent_messages[$i]['ms_body'];
                                if($i == 0){
                                    echo "<div class=\"col-lg-9\" id=\"sent_div_$i\" style=\"background-color: white; border: 0;\">";
                                }
                                else{
                                    echo "<div class=\"col-lg-9\" id=\"sent_div_$i\" style=\"background-color: white; border: 0; display:none;\">";
                                }
                                
                                echo "<form id=\"sent_form_$i\">
                                    <!--<button class=\"btn btn-sm btn-primary form-inline\" id=\"reply_message_$i\" type=\"submit\">Reply</button>
                                    <button class=\"btn btn-sm btn-primary form-inline\" id=\"delete_message_$i\" type=\"submit\">Delete</button>
                                    --><textarea form=\"message_ name=\"sent_content_$i\" style=\"resize:none\" rows=\"10\" class=\"form-control\" readonly>$body</textarea>
                                        </form>
                                        </div>";
                            } 
                        }
                        else{
                            echo "<h4>No Messages</h4>";
                        }
                    
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
