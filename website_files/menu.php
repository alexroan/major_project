<?php
session_start();

function GetMenu($file_name){
    $files = Array("Home" => "home.php", "My Activity" => "activity.php", 
        "Messages" => "messages.php", "My Profile" => "profile.php", 
        "Find a Journey" => "search_journey.php", "Post a Journey" => "post_journey.php");
    
    $returned = "<div class=\"masthead\" style=\"padding-top: 50px\">
        <ul class=\"nav nav-justified\">";
    
    foreach($files as $key => $value){
        if($file_name == $value){
            $returned = $returned."<li class=\"active\"><a href=\"$value\">$key</a></li>";
        }
        else{
            $returned = $returned."<li><a href=\"$value\">$key</a></li>";
        }
    }                    
    $returned = $returned."</ul></div>"; 
    return $returned;
}
?>
