<?php

    /**
     * Gets a single hitch_request using its hr_pk. request_type = 'get_hitch_request'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function get_hitch_request($connection, $post_data){
        $returned = null;
        $hr_pk = filter_var($post_data['hr_pk'], FILTER_VALIDATE_INT);
        
        $sql_string = "select * from hitch_request where hr_pk = $hr_pk";
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }


    /**
     * accepts hitch request using its PK. request_type = 'accept_hitch_request'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function accept_hitch_request($connection, $post_data){
        $returned = null;
        $hr_pk = filter_var($post_data['hr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "update hitch_request set hr_response = 1, hr_response_date = now() where hr_pk = $hr_pk returning hr_jr";
        
        $response = pg_query($connection, $sql_string);
        $returned_array = pg_fetch_array($response);
        if($returned_array == false){
            $returned = pg_last_error($connection);
        }
        else{
            $ht_jr = $returned_array[0];
            $sql_string = "insert into hitch (ht_jr, ht_hr) values ($ht_jr, '$hr_pk');
                    update journey set jr_spaces_available = (jr_spaces_available-1) where jr_pk = $ht_jr returning *";
            $response = pg_query($connection, $sql_string);
            if($response){
                $returned = pg_fetch_row($response);
            }
            else{
                $returned = pg_last_error($connection);
            }
        }     
        return $returned;
    } 
    
    
    /**
     * NOT FULLY TESTED
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function accept_hitch_request_with_waypoints($connection, $post_data){
        $returned = null;
        $hr_pk = filter_var($post_data['hr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        //Get the target hitch_request an related journey information
        $sql_string = "select * from hitch_request hr left join journey jr on 
            hr.hr_jr = jr.jr_pk where hr.hr_pk = $hr_pk";
        $response = pg_query($connection, $sql_string);
        if($response){
            $hitch_request_array = pg_fetch_array($response);
            $jr_pk = $hitch_request_array['hr_jr'];
            $origin = $hitch_request_array['jr_origin'];
            $destination = $hitch_request_array['jr_destination'];
            $waypoint_1 = $hitch_request_array['hr_waypoint_1'];
            $waypoint_2 = $hitch_request_array['hr_waypoint_2'];
            
            //Get all hitch requests accepted for this journey to adjust route
            $sql_string = "select * from hitch_request where hr_jr = $jr_pk and hr_response = 1";
            $response = pg_query($connection, $sql_string);
            if($response){                
                $waypoint_array = Array();
                //populate $waypoint_array with target hitch_request information
                if(!is_null($waypoint_1)){
                    array_push($waypoint_array, $waypoint_1);
                }
                if(!is_null($waypoint_2)){
                    array_push($waypoint_array, $waypoint_2);
                }
                
                //fetch other accepted hitch_requests data for target journey
                //populate $waypoint_array with other accepted hitch_request data
                if(pg_num_rows($response) > 0){
                    $accepted_requests_array = pg_fetch_all($response);
                    //print_r($accepted_requests_array);
                    for($i = 0; $i < count($accepted_requests_array); $i++){
                        $database_waypoint_1 = $accepted_requests_array[$i]['hr_waypoint_1'];
                        $database_waypoint_2 = $accepted_requests_array[$i]['hr_waypoint_2'];
                        if((!in_array($database_waypoint_1, $waypoint_array, true)) && (!is_null($database_waypoint_1))){
                            array_push($waypoint_array, $database_waypoint_1);
                        }
                        if((!in_array($database_waypoint_2, $waypoint_array, true)) && (!is_null($database_waypoint_2))){
                            array_push($waypoint_array, $database_waypoint_2);
                        }
                    }
                }                
                
                //build google directions api url to get direction data
                $journey_details_url = build_directions_api_url($origin, $destination, $waypoint_array);
                //print_r($journey_details_url);
                
                
                //update database with new journey, hitch, hitch_request and journey_step data
                //MULTIPLE LEGS AND STEPS
                $journey_details_json = @file_get_contents($journey_details_url);
                $journey_details = json_decode($journey_details_json, true);
                $journey_legs = $journey_details['routes'][0]['legs'];
                $sql_string = "insert into journey_step_temp (st_jr, st_step_order, st_latitude, st_longitude) 
                    (select js_jr, js_step_order, js_latitude, js_longitude from journey_step where js_jr = $jr_pk);
                    delete from journey_step where js_jr = $jr_pk";
                $response = pg_query($connection, $sql_string);
                if($response){
                    $sql_string = "insert into journey_step (js_jr, js_step_order, js_latitude, js_longitude) values ";
                    $count = 0;
                    for($i = 0; $i < count($journey_legs); $i++){
                        $this_leg = $journey_legs[$i];
                        for($j = 0; $j < count($this_leg['steps']); $j++){
                            $this_step = $this_leg['steps'][$j];
                            $this_lat = $this_step['end_location']['lat'];
                            $this_lng = $this_step['end_location']['lng'];
                            if(((count($journey_legs) - $i) == 1) && ((count($this_leg['steps']) - $j) == 1)){
                                $sql_string = $sql_string."($jr_pk, $count, $this_lat, $this_lng)";
                            }
                            else{
                                $sql_string = $sql_string."($jr_pk, $count, $this_lat, $this_lng),";
                            }
                            $count++;                            
                        }
                    }
                    $response = pg_query($connection, $sql_string);
                    if($response){
                        $sql_string = "update hitch_request set hr_response_date = now(), hr_response = 1 where hr_pk = $hr_pk;
                                insert into hitch (ht_jr, ht_hr) values ($jr_pk, $hr_pk);
                                update journey set jr_spaces_available = (jr_spaces_available - 1) where jr_pk = $jr_pk;
                                delete from journey_step_temp where st_jr = $jr_pk";
                        $response = pg_query($connection, $sql_string);
                        if($response){
                            $returned = "Acceptance of hitch process completed";
                        }
                        else{
                            $returned = pg_last_error($connection);
                        }                                                      
                    }
                    else{
                        //Deleted old steps from journey_step but could not insert new journey_step data
                        $returned = pg_last_error();
                        $sql_string = "insert into journey_step (js_jr, js_step_order, js_latitude, js_longitude) 
                                (select st_jr, st_step_order, st_latitude, st_longitude from journey_step_temp where st_jr = $jr_pk);
                                delete from journey_step_temp where st_jr = $jr_pk";
                        $response = pg_query($connection, $sql_string);
                    }
                }
                else{
                    //could not delete old journey steps so new ones were not inserted
                    $returned = pg_last_error($connection);
                }                
            }
            else{
                //TODO
            }          
        }
        else{
            //TODO
        }
        return $returned;
    }
    
    
    /**
     * declines hitch request using its PK. request_type = 'decline_hitch_request'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function decline_hitch_request($connection, $post_data){
        $returned = null;
        $hr_pk = filter_var($post_data['hr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "update hitch_request set hr_response = 0, hr_response_date = now() where hr_pk = $hr_pk returning *";
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        } 
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }

    /**
     * gets all hitch requests sent to a certain user using their ps_email. request_type = 'get_hitch_requests'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function get_hitch_requests($connection, $post_data){
        $returned = null;
        $email = filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
        
        $sql_string = "select * from hitch_request where hr_jr in (select jr_pk from journey where jr_ps_email = '$email') and hr_response_date is null";
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_all($response);
        }   
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }

    /**
     * requests hitch to a particular journey. request_type = 'request_hitch'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function request_hitch($connection, $post_data){
        $returned = null;
        $email = filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
        $jr_pk = filter_var($post_data['jr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "insert into hitch_request (hr_jr, hr_ps_email) values 
            ($jr_pk, '$email') returning *";
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        }
        else{            
            $returned = pg_last_error($connection);
        }
        return $returned;
    }
    
    
    /**
     * request hitch with option of "waypoint_1" and "waypoint_2" in post data
     * can post one, or both. If one, preferably "waypoint_1".
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function request_hitch_with_waypoints($connection, $post_data){
        $returned = null;
        $email = filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
        $jr_pk = filter_var($post_data['jr_pk'], FILTER_SANITIZE_NUMBER_INT);
        $waypoint_1 = filter_var($post_data['waypoint_1'], FILTER_SANITIZE_STRING);
        $waypoint_2 = filter_var($post_data['waypoint_2'], FILTER_SANITIZE_STRING);
        $sql_string = "";
        if(!$waypoint_2){
            $sql_string = "insert into hitch_request (hr_jr, hr_ps_email, hr_waypoint_1) values
                ($jr_pk, '$email', '$waypoint_1')";
        }
        else{
            $sql_string = "insert into hitch_request (hr_jr, hr_ps_email, hr_waypoint_1, hr_waypoint_2)
                values ($jr_pk, '$email', '$waypoint_1', '$waypoint_2') returning *";
        }
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }
    
    
    /**
     * canceles a particular hitch. request_type = 'cancel_hitch'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function cancel_hitch($connection, $post_data){
        $returned = null;
        $ht_pk = filter_var($post_data['ht_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "delete from hitch where ht_pk = $ht_pk returning ht_jr, ht_hr";
        $response = pg_query($connection, $sql_string);
        if($response){
            $response_array = pg_fetch_row($response);
            $jr_pk = $response_array[0];
            $hr_pk = $response_array[1];
            $sql_string = "update journey set jr_spaces_available = (jr_spaces_available + 1) where jr_pk = $jr_pk;
                    update hitch_request set hr_response = 0 where hr_pk = $hr_pk returning *";
            $response = pg_query($connection, $sql_string);
            if($response){
                $returned = pg_fetch_row($response);
            }
            else{
                $returned = pg_last_error($connection);
            }
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }

?>
