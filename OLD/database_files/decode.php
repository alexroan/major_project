<?php 
    
    /**
     * deals with post data sent to database files.
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function decode_post($connection, $post_data){
        $request_type = $post_data['request'];
        //PERSON ACTIONS
        //DONE TESTING
        if($request_type == "register_person"){
            encode_json($request_type, register_person($connection, $post_data));
        }
        else if ($request_type == "get_person"){
            encode_json($request_type, get_person($connection, $post_data));
        }
        
        //JOURNEY ACTIONS
        //DONE TESTING
        else if ($request_type == "post_journey"){
            encode_json($request_type, post_journey($connection, $post_data));
        }
        else if ($request_type == "search_journey"){
            encode_json($request_type, search_journey($connection, $post_data));
        }
        else if ($request_type == "cancel_journey"){
            encode_json($request_type, cancel_journey($connection, $post_data));
        }
        else if ($request_type == "get_journey"){
            encode_json($request_type, get_journey($connection, $post_data));
        }
        else if ($request_type == "get_journey_with_hitchers"){
            encode_json($request_type, get_journey_with_hitchers($connection, $post_data));
        }
        else if ($request_type == "get_my_journeys"){
            encode_json($request_type, get_my_journeys($connection, $post_data));
        }
        
        //HITCH ACTIONS
        else if ($request_type == "request_hitch"){
            encode_json($request_type, request_hitch($connection, $post_data));
        }
        else if ($request_type == "request_hitch_with_waypoints"){
            encode_json($request_type, request_hitch_with_waypoints($connection, $post_data));
        }
        else if ($request_type == "get_hitch_request"){
            encode_json($request_type, get_hitch_request($connection, $post_data));
        }
        else if ($request_type == "get_hitch_requests"){
            encode_json($request_type, get_hitch_requests($connection, $post_data));
        }
        else if ($request_type == "accept_hitch_request"){
            encode_json($request_type, accept_hitch_request($connection, $post_data));
        }
        else if ($request_type == "accept_hitch_request_with_waypoints"){
            encode_json($request_type, accept_hitch_request_with_waypoints($connection, $post_data));
        }
        else if ($request_type == "decline_hitch_request"){
            encode_json($request_type, decline_hitch_request($connection, $post_data));
        }
        else if ($request_type == "cancel_hitch"){
            encode_json($request_type, cancel_hitch($connection, $post_data));
        }
    }

    
    /**
     * UNUSED SO FAR
     * Use UID in database every time user logs in, use that instead of password
     * to
     * @param type $connection connection to psql database
     * @param type $post_data an array of post data sent to be parsed
     * @return boolean Whether or not the credentials were correct
     */
    function check_credentials($connection, $post_data){
        $returned = null;
        $email = filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
        $password = filter_var($post_data['password'], FILTER_SANITIZE_STRING);
        
        $sql_string = "select * from person where ps_email = '$email' and ps_password = '$password'";
        $response = pg_query($connection, $sql_string);
        if(pg_num_rows($response) == 1){
            $returned = true;
        }
        else{
            $returned = false;
        }
        return $returned;
    }
   
?>
