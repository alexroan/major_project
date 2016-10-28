<?php

require_once 'table.php';

/**
 * Journey_Step model class modelling journey_step table in database
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Journey_Step extends  Table{
    
    var $js_pk = "";
    var $js_jr = "";
    var $js_step_order = "";
    var $js_latitude = "";
    var $js_longitude = "";
    
    var $st_pk = "";
    
    /**
     * Constructor. Calls Load() if $js_pk != ""
     * @param Integer $js_pk
     */
    function __construct($js_pk = "") {
        $this->ConnectToDatabase();
        if($js_pk != ""){
            $this->load($js_pk);
        }
    }
    
    /**
     * Constructor. Calls Load() if $js_pk != ""
     * @param Integer $js_pk
     */
    function Journey_Step($js_pk = ""){
        $this->__construct($js_pk);
    }
    
    
    function Load($js_pk = ""){
        $returned = false;
        if($js_pk != ""){
            $sql_string = "select * from journey_step where js_pk = $js_pk";
            $response = pg_query($sql_string);
            if(pg_num_rows($response) == 1){
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
        if($this->js_jr != "" && $this->js_step_order != "" && $this->js_latitude != "" && $this->js_longitude != ""){
            $sql_string = "insert into journey_step (js_jr, js_step_order, js_latitude, js_longitude)
                values ($this->js_jr, $this->js_step_order, $this->js_latitude, $this->js_longitude) 
                    returning js_pk";
            $response = pg_query($sql_string);
            if($response){
                $response_array = pg_fetch_assoc($response);
                $this->js_pk = $response_array['js_pk'];
                $returned = true;
            }
        }
        return $returned;
    }
    
    function CreateTemp(){
        $returned = false;
        if($this->js_jr != "" && $this->js_step_order != "" && $this->js_latitude != "" && $this->js_longitude != ""){
            $sql_string = "insert into journey_step_temp (st_jr, st_step_order, st_latitude, st_longitude)
                values ($this->js_jr, $this->js_step_order, $this->js_latitude, $this->js_longitude) returning st_pk";
            $response = pg_query($sql_string);
            if($response){
                $response_array = pg_fetch_assoc($response);
                $this->st_pk = $response_array['st_pk'];
                $returned = true;
            }
        }
        return $returned;
    }
    
    
    function Update(){
        $returned = false;
        if($this->js_pk != ""){
            $sql_string = "update journey_step set js_jr = $this->js_jr, 
                js_step_order = $this->js_step_order, js_latitude = $this->js_latitude, 
                js_longitude = $this->js_longitude where js_pk = $this->js_pk";
            
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
            }
        }
        return $returned;
    }
    
    
    function Delete(){
        $returned = false;
        if($this->js_pk != ""){
            $sql_string = "delete from journey_step where js_pk = $this->js_pk";
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
                /*$field_array = get_object_vars($this);
                foreach($field_array as $key => $value){
                    $this->$key = "";
                }*/
            }
        }
        return $returned;
    }
    
    /**
     * Deletes the temporary journey steps in journey_step_temp table
     * @return boolean success
     */
    function DeleteTemp(){
        $returned = false;
        if($this->st_pk != ""){
            $sql_string = "delete from journey_step_temp where st_pk = $this->st_pk";
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
            }
        }
        return $returned;
    }
    
}


?>
