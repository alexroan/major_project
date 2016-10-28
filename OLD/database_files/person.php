<?php 


    /**
     * registers a person obejct to the database. request_type = 'register_person'
     * @param database_connection $connection connection to psql database
     * @param array $post_data an array of post data sent to be parsed
     */
    function register_person($connection, $post_data){
        $returned = null;
        $person_array = Array();
        
        $person_array['ps_email'] = strtoupper(filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL));
        $person_array['ps_first_name'] = filter_var($post_data['first_name'], FILTER_SANITIZE_STRING);
        $person_array['ps_last_name'] = filter_var($post_data['last_name'], FILTER_SANITIZE_STRING);
        $person_array['ps_password'] = filter_var($post_data['password'], FILTER_SANITIZE_STRING);
        $person_array['ps_home_1'] = filter_var($post_data['home_1'], FILTER_SANITIZE_STRING);
        $person_array['ps_home_2'] = filter_var($post_data['home_2'], FILTER_SANITIZE_STRING);
        $person_array['ps_frequent_destination_1'] = filter_var($post_data['freq_dest_1'], FILTER_SANITIZE_STRING);
        $person_array['ps_frequent_destination_2'] = filter_var($post_data['freq_dest_2'], FILTER_SANITIZE_STRING);
        $person_array['ps_frequent_destination_3'] = filter_var($post_data['freq_dest_3'], FILTER_SANITIZE_STRING);
        $person_array['ps_current_location'] = filter_var($post_data['current_location'], FILTER_SANITIZE_STRING);        
        
        $sql_string = sqlify_insert_returning($person_array, "person");     
        //print_r($sql_string);
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }
    
    
    function get_person($connection, $post_data){
        $returned = null;
        $email = strtoupper(filter_var($post_data['email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL));
        
        $sql_string = "select * from person where ps_email = '$email'";
        
        $response = pg_query($connection, $sql_string);
        if($response){
            $returned = pg_fetch_row($response);
        }
        else{
            $returned = pg_last_error($connection);
        }
        return $returned;
    }

?>
