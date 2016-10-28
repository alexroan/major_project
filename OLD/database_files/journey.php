<?php 

    /**
     * Gets a particular journey and encodes the response
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function get_journey($connection, $post_data){
        $returned = null;
        $jr_pk = filter_var($post_data['jr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "select * from journey where jr_pk = $jr_pk";
        
        $response = pg_query($sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }
    
    
    /**
     * Gets a particular journey with all accepted hitchers and encodes the response
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function get_journey_with_hitchers($connection, $post_data){
        $returned = null;
        $jr_pk = filter_var($post_data['jr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "select * from journey jr left join hitch_request hr on hr_jr = jr_pk where hr.hr_response = 1 and jr.jr_pk = $jr_pk";
        
        $response = pg_query($sql_string);
        if($response){
            $returned = pg_fetch_all($response);
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }
    
    /**
     * 
     * @param type $connection
     * @param type $post_data
     * @return type
     */
    function get_my_journeys($connection, $post_data){
        $returned = null;
        $email = filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
        $sql_string = "select * from journey where jr_ps_email = '$email'";
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
     * Posts a journey object to the database. request_type = 'post_journey'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function post_journey($connection, $post_data){
        $returned = null;
        //PARSE POST DATA
        $email = filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
        $origin = filter_var($post_data['origin'], FILTER_SANITIZE_STRING);
        $destination = filter_var($post_data['destination'], FILTER_SANITIZE_STRING);
        $etd = filter_var($post_data['etd'], FILTER_SANITIZE_STRING);
        $eta = filter_var($post_data['eta'], FILTER_SANITIZE_STRING);
        $spaces_available = filter_var($post_data['spaces_available'], FILTER_SANITIZE_NUMBER_INT);
        $total_spaces = filter_var($post_data['total_spaces'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($post_data['description'], FILTER_SANITIZE_STRING);
        $extra_distance = filter_var($post_data['extra_distance'], FILTER_SANITIZE_NUMBER_FLOAT);
        
        //PARSE JOURNEY FROM GOOGLE MAPS API
        $journey_details_url = "http://maps.googleapis.com/maps/api/directions/json?origin=".str_replace(' ','+', $origin, $x)."&destination=".str_replace(' ', '+', $destination, $y)."&region=uk&sensor=false";
        $journey_details_json = @file_get_contents($journey_details_url);
        $journey_details = json_decode($journey_details_json, true);
        $journey = $journey_details['routes'][0]['legs'][0];
        $distance = $journey['distance']['value']/1000;
        $start_lat = $journey['start_location']['lat'];
        $start_lng = $journey['start_location']['lng'];
        $end_lat = $journey['end_location']['lat'];
        $end_lng = $journey['end_location']['lng'];
        
        $steps = $journey['steps'];

        //INSERT JOURNEY INTO DATABASE
        $sql_string = "insert into journey (jr_ps_email, jr_origin, jr_destination,
            jr_etd, jr_eta, jr_spaces_available, jr_total_spaces, jr_description, jr_register_date,
            jr_extra_distance, jr_origin_latitude, jr_origin_longitude,
            jr_destination_latitude, jr_destination_longitude, jr_total_distance) 
            values ('$email','$origin','$destination','$etd',
            '$eta',$spaces_available,$total_spaces,'$description', now(),
            $extra_distance, $start_lat, $start_lng
            , $end_lat, $end_lng, $distance) returning jr_pk";      
        $response = pg_query($connection, $sql_string);
        
        //IF JOURNEY INSERTED CORRECTLY INSERT EACH STEP
        if($response){
            $row = pg_fetch_row($response);
            $jr_pk = $row[0];
            $sql_string = "insert into journey_step (js_jr, js_step_order, js_latitude, js_longitude) values ";
            for($i=0; $i<(count($steps)-1); $i++){
                $this_lat = $steps[$i]['end_location']['lat'];
                $this_lng = $steps[$i]['end_location']['lng'];
                if($i == (count($steps)-2)){
                    $sql_string = $sql_string."($jr_pk,$i,$this_lat,$this_lng)";
                }
                else{
                    $sql_string = $sql_string."($jr_pk,$i,$this_lat,$this_lng),";
                }                
            }            
            $response = pg_query($connection, $sql_string);
            if($response){
                $sql_string = "select * from journey where jr_pk = $jr_pk";
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
                $sql_string = "delete from journey where jr_pk = $jr_pk";
                $response = pg_query($connection, $sql_string);
            }
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }
    
    /**
     * Sets jr_is_cancelled flag in database from 0 to 1
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function cancel_journey($connection, $post_data){
        $returned = null;
        $jr_pk = filter_var($post_data['jr_pk'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql_string = "update journey set jr_is_cancelled = 1 where jr_pk = $jr_pk returning *";
        
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
     * Searches for journeys. request_type = 'search_journey'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function search_journey($connection, $post_data){   
        $returned = null;
        $origin = filter_var($post_data['origin'], FILTER_SANITIZE_STRING);
        $destination = filter_var($post_data['destination'], FILTER_SANITIZE_STRING);
        $date_1 = filter_var($post_data['date_1'], FILTER_SANITIZE_STRING);
        $date_2 = filter_var($post_data['date_2'], FILTER_SANITIZE_STRING);
        
        $origin_geo = geolocate($origin);        
        $origin_location_data = $origin_geo['results'][0]['geometry']['location'];
        $origin_lat = $origin_location_data['lat'];
        $origin_lng = $origin_location_data['lng'];
        
        $destination_geo = geolocate($destination);
        $destination_location_data = $destination_geo['results'][0]['geometry']['location'];
        $destination_lat = $destination_location_data['lat'];
        $destination_lng = $destination_location_data['lng'];
                
 
        $sql_string = construct_query_string($origin, $destination, $origin_lat, $origin_lng, $destination_lat, $destination_lng, $date_1, $date_2);
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
         * Constructs sql query from parameters.
         * @param string $origin
         * @param string $destination
         * @param real $origin_lat
         * @param real $origin_lng
         * @param real $destination_lat
         * @param real $destination_lng
         * @param string $date_1
         * @param string $date_2
         * @return string $sql_string sql request to be sent to the database
         */
        function construct_query_string($origin, $destination, $origin_lat, $origin_lng, $destination_lat, $destination_lng, $date_1, $date_2){
            $sql_string = "select * from journey where jr_origin = '$origin' and jr_destination = '$destination' 
                and jr_etd >= '$date_1' and jr_etd <= '$date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                select * from journey where jr_origin_latitude - $origin_lat < 0.04532
                and jr_origin_latitude - $origin_lat > - 0.04532 and jr_origin_longitude - $origin_lng
                < 0.041 and jr_origin_longitude - $origin_lng > -0.041 and jr_destination_latitude
                - $destination_lat < 0.04532 and jr_destination_latitude - $destination_lat > -0.04532
                and jr_destination_longitude - $destination_lng < 0.041 and jr_destination_longitude
                - $destination_lng > -0.041 and jr_etd >= '$date_1' and jr_etd <= '$date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                select * from journey where jr_pk in (select distinct js_jr from journey_step where js_jr in (select jr_pk from journey where jr_origin_latitude - $origin_lat <= 0.04532
                and jr_origin_latitude - $origin_lat >= - 0.04532 and jr_origin_longitude - $origin_lng
                <= 0.041 and jr_origin_longitude - $origin_lng >= -0.041) and js_latitude - $destination_lat >= -0.3153
                and js_latitude - $destination_lat <= 0.3153 and js_longitude - $destination_lng >= -0.35 and
                js_longitude - $destination_lng <= 0.35) and jr_etd >= '$date_1' and jr_etd <= '$date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                select * from journey where jr_pk in (select distinct js_jr from journey_step where js_jr in (select jr_pk from journey where jr_destination_latitude - $destination_lat <= 0.04532
                and jr_destination_latitude - $destination_lat >= - 0.04532 and jr_destination_longitude - $destination_lng
                <= 0.041 and jr_destination_longitude - $destination_lng >= -0.041) and js_latitude - $origin_lat >= -0.3153
                and js_latitude - $origin_lat <= 0.3153 and js_longitude - $origin_lng >= -0.35 and
                js_longitude - $origin_lng <= 0.35) and jr_etd >= '$date_1' and jr_etd <= '$date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                select * from journey where jr_pk in (select distinct a.js_jr from journey_step a inner join journey_step b on a.js_jr = b.js_jr 
                where a.js_latitude - $origin_lat >= -0.1 and a.js_latitude - $origin_lat <= 0.1 and 
                a.js_longitude - $origin_lng >= -0.09009 and a.js_longitude - $origin_lng <= 0.09009 and 
                b.js_latitude - $destination_lat >= -0.1 and b.js_latitude - $destination_lat <= 0.1 and 
                b.js_longitude - $destination_lng >= -0.09009 and b.js_longitude - $destination_lng <= 0.09009)
                and jr_etd >= '$date_1' and jr_etd <= '$date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0";
            //print_r($sql_string);
            return $sql_string;
        }

        

?>
