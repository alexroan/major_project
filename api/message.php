<?php
require 'table.php';

/**
 * Message model class modelling the message table in the database
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Message extends Table{
    
    var $ms_pk = "";
    var $ms_ps_sender = "";
    var $ms_ps_receiver = "";
    var $ms_table_ref = "";
    var $ms_table_ref_pk = "";
    var $ms_title = "";
    var $ms_body = "";
    var $ms_sent_date = "";
    var $ms_read_date = "";
    
    /**
     * Constructor. Calls Load() if $ms_pk != ""
     * @param Integer $ms_pk
     */
    function __construct($ms_pk = "") {
        $this->ConnectToDatabase();
        if($ms_pk != ""){
            $this->Load($ms_pk);
        }
    }
    
    /**
     * Constructor. Calls Load() if $ms_pk != ""
     * @param Integer $ms_pk
     */
    function Message($ms_pk = ""){
        $this->__construct($ms_pk);
    }
    
    function Load($ms_pk = ""){
        $returned = false;
        if($ms_pk != ""){
            $sql_string = "select * from message where ms_pk = $ms_pk";
            $response = pg_query($sql_string);
            if($response){
                $response_array = pg_fetch_assoc($response);
                $field_array = get_object_vars($this);
                foreach($field_array as $key => $value){
                    $this->$key = $response_array[$key];
                }
                $returned = true;
            }
        }
        return $returned;
    }
    
    function Create() {
        $returned = false;
        if($this->ms_ps_sender != "" && $this->ms_ps_receiver != ""){
            $sql_string = "insert into message (ms_ps_sender, ms_ps_receiver, 
                ms_table_ref, ms_table_ref_pk, ms_title, ms_body) values 
                ('$this->ms_ps_sender', '$this->ms_ps_receiver', '$this->ms_table_ref',
                '$this->ms_table_ref_pk', '$this->ms_title', '$this->ms_body') returning ms_pk";
            $response = pg_query($sql_string);
            if($response){
                $response_array = pg_fetch_assoc($response);
                $this->ms_pk = $response_array['ms_pk'];
                $returned = true;
            }
        }
        return $returned;
    }
    
    /**
     * Only updates ms_date_read field.
     * @return boolean
     */
    function Update(){
        $returned = false;
        if($this->ms_pk != ""){
            $sql_string = "update message set ms_read_date = now() where ms_pk = $this->ms_pk";
            $response = pg_query($sql_string);
            if($response){
                $returned = true;                
            }
        }
        return $returned;
    }
    
    /**
     * 
     * @return boolean
     */
    function Delete(){
        $returned = false;
        if($this->ms_pk != ""){
            $sql_string = "delete from message where ms_pk = '$this->ms_pk'";
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
