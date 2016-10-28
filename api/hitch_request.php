<?php

require_once 'table.php';

/**
 * @author Alex Roan <alr16@aber.ac.uk>
 * Hitch_Request model class. Corresponding table: hitch_request
 */
class Hitch_Request extends Table{
    
    var $hr_pk = "";
    var $hr_jr = "";
    var $hr_ps_email = "";
    var $hr_request_date = "";
    var $hr_response_date = "";
    var $hr_response = "";
    var $hr_waypoint_1 = "";
    var $hr_waypoint_2 = "";
    
    /**
     * Constructor, calls Load() if $hr_pk != ""
     * @param Integer $hr_pk
     */
    function __construct($hr_pk = "") {
        $this->ConnectToDatabase();
        if($hr_pk != ""){
            $this->load($hr_pk);
        }
    }
    
    /**
     * Constructor, calls Load() if $hr_pk != ""
     * @param Integer $hr_pk
     */
    function Hitch_Request($hr_pk = ""){
        $this->__construct($hr_pk);
    }
    
    
    function Load($hr_pk = ""){
        $returned = false;
        if($hr_pk != ""){
            $sql_string = "select * from hitch_request where hr_pk = $hr_pk";
            $response = pg_query($sql_string);
            if(pg_num_rows($response)){
                $response_array = pg_fetch_assoc($response);
                foreach($response_array as $key => $value){
                    $this->$key = htmlentities($response_array[$key]);
                }
                $returned = true;
            }
        }
        return $returned;
    }
        
    
    function Create(){
        $returned = false;
        if($this->hr_jr != "" && $this->hr_ps_email != ""){
            $sql_string = "insert into hitch_request 
                (hr_jr, hr_ps_email, hr_waypoint_1, hr_waypoint_2)
                values ($this->hr_jr, '$this->hr_ps_email', '$this->hr_waypoint_1', '$this->hr_waypoint_2') 
                    returning hr_pk";
            
            $response = pg_query($sql_string);
            if($response){
                $response_array = pg_fetch_assoc($response);
                $this->hr_pk = $response_array['hr_pk'];
                $returned = true;
            }
        } 
        return $returned;
    }
    
    
    function Update(){
        $returned = false;
        if($this->hr_pk != ""){
            if($this->hr_response == ""){
                $sql_string = "update hitch_request set hr_jr = $this->hr_jr, 
                    hr_ps_email = '$this->hr_ps_email', hr_request_date = '$this->hr_request_date',
                    hr_waypoint_1 = '$this->hr_waypoint_1', hr_waypoint_2 = '$this->hr_waypoint_2' 
                    where hr_pk = $this->hr_pk";
            }
            else{
                $sql_string = "update hitch_request set hr_jr = $this->hr_jr, 
                    hr_ps_email = '$this->hr_ps_email', hr_request_date = '$this->hr_request_date',
                    hr_response_date = '$this->hr_response_date', hr_response = $this->hr_response,
                    hr_waypoint_1 = '$this->hr_waypoint_1', hr_waypoint_2 = '$this->hr_waypoint_2' 
                    where hr_pk = $this->hr_pk";
            }
            
            
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
            }
        }
        return $returned;        
    }
    
    
    function Delete(){
        $returned = false;
        if($this->hr_pk != ""){
            $sql_string = "delete from hitch_request where hr_pk = $this->hr_pk";
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
    
}

?>
