<?php

    /**
     * creats an sql insert string
     * @param array $my_array associative array of table_name => value pairs
     * @param string $table_name name of the table being inserted into
     * @return string sql insert string loaded with data ready to be sent to server
     */
    function sqlify_insert($my_array, $table_name){
        $sql_string = "insert into $table_name (";
        foreach($my_array as $key => $value){
            if($value != null){                    
                $sql_string = $sql_string."$key,";
            }
            else{
                unset($my_array[$key]);
            }           
        }        
        $sql_string= $sql_string.") values (";          
        foreach($my_array as $key => $value){         
            if(gettype($value) == "string"){
                $sql_string = $sql_string."'$value',"; 
            }
            else{
                $sql_string = $sql_string.($value).",";
            }             
        }
        $sql_string = $sql_string.")";
        $sql_string = str_replace(",)", ")", $sql_string);
        return $sql_string;
    }
    
    /**
     * creats an sql insert string with a returning * suffix
     * @param array $my_array associative array of table_name => value pairs
     * @param string $table_name name of the table being inserted into
     * @return string sql insert string loaded with data ready to be sent to server
     */
    function sqlify_insert_returning($my_array, $table_name){
        $sql_string = sqlify_insert($my_array, $table_name)." returning *";
        return $sql_string;
    }
    
    
    /**
    * uses Google geolocate API to locate a place name
    * @param string $place_name place name to be located
    * @return array $place_data data on the $place_name provided
    */
   function geolocate($place_name){
       $geo_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".str_replace(' ', '+', $place_name,$x)."&sensor=false";
       $place_json_data = @file_get_contents($geo_url);
       $place_data = json_decode($place_json_data, true);
       $status = $place_data['status'];
       if($status == "OVER_QUERY_LIMIT"){
           return null;
       }
       else{
           return $place_data;
       }
   }
   
   
   
    function build_directions_api_url($origin, $destination, $waypoint_array){
        $journey_details_url = "http://maps.googleapis.com/maps/api/directions/json?origin=".str_replace(' ','+', $origin, $x)."&destination=".str_replace(' ', '+', $destination, $y)."&waypoints=optimize:true|";
        for($j = 0; $j < count($waypoint_array); $j++){
            if((count($waypoint_array) - $j) == 1){
                $journey_details_url = $journey_details_url.str_replace(' ','+', $waypoint_array[$j], $x);
            }
            else{
                $journey_details_url = $journey_details_url.str_replace(' ','+', $waypoint_array[$j], $x)."|";
            }
        }
        $journey_details_url = $journey_details_url."&amp;region=uk&sensor=false";                
        return $journey_details_url;
    }

?>
