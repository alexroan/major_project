<?php
require_once 'table.php';

/**
 * Person model class modelling person table in database
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Person extends Table{
        
    var $ps_email = "";
    var $ps_first_name = "";
    var $ps_last_name = "";
    var $ps_password = "";
    var $ps_home_1 = "";
    var $ps_home_2 = "";
    var $ps_frequent_destination_1 = "";
    var $ps_frequent_destination_2 = "";
    var $ps_frequent_destination_3 = "";
    var $ps_current_location = "";
    var $ps_register_date = "";
    
    /**
     * Constructor. Calls Load() if $ps_email != ""
     * @param String $ps_email
     */
    function __construct($ps_email = "") {
        $this->ConnectToDatabase();
        if($ps_email != ""){
            $this->load($ps_email);
        }
    }
    
    /**
     * Constructor. Calls Load() if $ps_email != ""
     * @param String $ps_email
     */
    function Person($ps_email = ""){
        $this->__construct($ps_email);
    }
    
    
    
    function Load($ps_email = ""){
        $returned = false;
        if($ps_email != ""){
            $sql_string = "select * from person where ps_email = '$ps_email'";
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
    
    function Create(){
        $returned = false;
        if ($this->ps_email != "" && $this->ps_first_name != "" && $this->ps_last_name != "" && $this->ps_password != ""){
            $sql_string = "insert into person (ps_email, ps_first_name, ps_last_name, ps_password, ps_home_1, ps_home_2, 
                ps_frequent_destination_1, ps_frequent_destination_2, ps_frequent_destination_3, ps_current_location) values 
                ('$this->ps_email','$this->ps_first_name','$this->ps_last_name',
                '$this->ps_password','$this->ps_home_1','$this->ps_home_2',
                '$this->ps_frequent_destination_1','$this->ps_frequent_destination_2',
                '$this->ps_frequent_destination_3','$this->ps_current_location') returning ps_register_date";
            $response = pg_query($sql_string);
            if ($response){
                $response_array = pg_fetch_assoc($response);
                $this->ps_register_date = $response_array['ps_register_date'];
                $returned = true;                
            }
        }
        return $returned;
    }    
    
    function Update(){
        $returned = false;
        if($this->ps_email != ""){
            $sql_string = "update person set ps_email = '$this->ps_email', ps_first_name = '$this->ps_first_name', 
                ps_last_name = '$this->ps_last_name', ps_password = '$this->ps_password', 
                ps_home_1 = '$this->ps_home_1', ps_home_2 = '$this->ps_home_2', 
                ps_frequent_destination_1 = '$this->ps_frequent_destination_1', 
                ps_frequent_destination_2 = '$this->ps_frequent_destination_2', 
                ps_frequent_destination_3 = '$this->ps_frequent_destination_3', 
                ps_current_location = '$this->ps_current_location' where ps_email = '$this->ps_email'";
            $response = pg_query($sql_string);
            if($response){
                $returned = true;
            }
        }
        return $returned;
    }        
    
    function Delete(){
        $returned = false;
        if($this->ps_email != ""){
            $sql_string = "delete from person where ps_email = '$this->ps_email'";
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
