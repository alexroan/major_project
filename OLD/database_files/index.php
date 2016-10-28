<?php 

/**
 * @filename index.php
 * @author alr16
 * Index file deals with all posts sent to the databse php files. Resulting data parsed by decode.php.
 */

    //session_name("database_session");
    //session_start();
    //error_reporting(0);
    
    require_once 'data.php';
    require_once 'decode.php';
    require_once 'encode.php';
    require_once 'person.php';
    require_once 'journey.php';
    require_once 'hitch.php';
    
    $post_data = json_decode(file_get_contents('php://input'), true);
    
    
    if($post_data){
        $connection = pg_connect("<INPUT PARAMS>");
	if($connection){            
            decode_post($connection, $post_data);
        }
        else{
            encode_post_details("connect", false, "could not connect to the database");
        }
    }
    else{
        encode_post_details("post", false, "no post data received"); 
    }

?>
