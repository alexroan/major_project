<?php

require_once 'table.php';

/**
 * Model class for Journey table in database
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Journey extends Table{
    
    var $jr_pk = "";
    var $jr_ps_email = "";
    var $jr_origin = "";
    var $jr_destination = "";
    var $jr_etd = "";
    var $jr_eta = "";
    var $jr_spaces_available = "";
    var $jr_total_spaces = "";
    var $jr_description = "";
    var $jr_register_date = "";
    var $jr_extra_distance = "35";
    var $jr_origin_latitude = "";
    var $jr_origin_longitude = "";
    var $jr_destination_latitude = "";
    var $jr_destination_longitude = "";
    var $jr_total_distance = "";
    var $jr_is_cancelled = "";
    
    /**
     * Constructor. Calls Load() if $jr_pk != ""
     * @param Integer $jr_pk
     */
    function __construct($jr_pk = "") {
        $this->ConnectToDatabase();
        if($jr_pk != ""){
            $this->load($jr_pk);
        }
    }
    
    /**
     * Constructor. Calls Load() if $jr_pk != ""
     * @param Integer $jr_pk
     */
    function Journey($jr_pk = ""){
        $this->__construct($jr_pk);
    }
    
    
    
    function Load($jr_pk = ""){
        $returned = false;
        if($jr_pk != ""){
            $sql_string = "select * from journey where jr_pk = $jr_pk";
            $response = pg_query($sql_string);
            if(pg_num_rows($response) == 1){                
                $response_array = pg_fetch_assoc($response);
                foreach($response_array as $key => $value){                    
                    if(isset($this->$key)){                        
                        $this->$key = htmlentities($response_array[$key]);                       
                    }                    
                }
                $returned = true;
            }            
        }
        return $returned;
    }   
    
    
    function Create() {
        $returned = false;
        if($this->jr_ps_email != "" && $this->jr_origin != "" && 
                $this->jr_destination != "" && $this->jr_etd != "" && $this->jr_eta != "" && 
                $this->jr_origin_latitude != "" && 
                $this->jr_origin_longitude != "" && $this->jr_destination_latitude != "" && 
                $this->jr_destination_longitude != "" && $this->jr_total_distance != ""){
            
            $sql_string  = "insert into journey (jr_ps_email, jr_origin, jr_destination, jr_etd, jr_eta, jr_spaces_available, jr_total_spaces, jr_description, jr_extra_distance, jr_origin_latitude, jr_origin_longitude, jr_destination_latitude, jr_destination_longitude, jr_total_distance) 
                values ('$this->jr_ps_email', '$this->jr_origin', '$this->jr_destination', 
                '$this->jr_etd', '$this->jr_eta', $this->jr_spaces_available, 
                $this->jr_total_spaces, '$this->jr_description', 
                $this->jr_extra_distance, $this->jr_origin_latitude, $this->jr_origin_longitude, 
                $this->jr_destination_latitude, $this->jr_destination_longitude, 
                $this->jr_total_distance) returning jr_pk";
            
            $response = pg_query($sql_string);
            if($response){
                $response_array = pg_fetch_assoc($response);
                $this->jr_pk = $response_array['jr_pk'];
                $returned = true;
            }
        }
        return $returned;
    }
    
    function Update(){
        $returned = false;
        if($this->jr_pk != ""){
            $sql_string  = "update journey set jr_ps_email = '$this->jr_ps_email', jr_origin = '$this->jr_origin', jr_destination = '$this->jr_destination', 
                jr_etd = '$this->jr_etd', jr_eta = '$this->jr_eta', jr_spaces_available = $this->jr_spaces_available, 
                jr_total_spaces = $this->jr_total_spaces, jr_description = '$this->jr_description', jr_register_date = '$this->jr_register_date', 
                jr_extra_distance = $this->jr_extra_distance, jr_origin_latitude = $this->jr_origin_latitude, jr_origin_longitude = $this->jr_origin_longitude, 
                jr_destination_latitude = $this->jr_destination_latitude, jr_destination_longitude = $this->jr_destination_longitude, 
                jr_total_distance = $this->jr_total_distance, jr_is_cancelled = $this->jr_is_cancelled where jr_pk = $this->jr_pk";
        
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
            }
        }
        return $returned;
    }
    
    function Delete(){
        $returned = false;
        if($this->jr_pk != ""){
            $sql_string = "delete from journey where jr_pk = $this->jr_pk";
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
                $field_array = get_object_vars($this);
                foreach($field_array as $key => $value){
                    $this->$key = "";                    
                }
            }
        }
        return $returned;
    }
    
    /**
     * Cancels Journey
     * @return boolean success
     */
    function Cancel(){
        $returned = false;
        if($this->jr_pk != ""){
            $sql_string = "update journey set jr_is_cancelled = 1 where jr_pk = $this->jr_pk";
            $response = pg_query($sql_string);
            if($response){
                $this->jr_is_cancelled = 1;
                $returned = true;
            }            
        }
        return $returned;
    }
    
}

?>
