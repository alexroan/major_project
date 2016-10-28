<?php
require_once 'api.php';

class Table extends API{
    
    /**
     * Loads entry from database table using primary key.
     * @param Integer PrimaryKey
     * @return boolean success
     */
    function Load(){
        
    }
    
    /**
     * Unloads the database table entry attributes from the object
     */
    function Unload(){
        $field_array = get_object_vars($this);
        foreach($field_array as $key => $value){
            $this->$key = "";
        }
    }
    
    /**
     * Creates entry in the database using current object attribute values
     * @return boolean success
     */
    function Create(){
        
    }
    
    /**
     * Updates entry in database table using current attribute values in object.
     * @return boolean success
     */
    function Update(){
        
    }
    
    /**
     * Deletes entry in database table using current primary key attribute value
     */
    function Delete(){
        
    }
    
    /*
    function LoadTopUsing($field_name = ""){
        $returned = false;
        if($field_name != ""){
            $object_name = get_class($this);
            $field = $this->Get($field_name);
            if(gettype($this->Get($field_name)) == "string"){
                $sql_string = "select * from $object_name where $field_name = '$field' limit 1";
            }
            else{
                $sql_string = "select * from $object_name where $field_name = '$field' limit 1";
            }
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
    }*/
}

?>
