<?php

/**
 * @author Alex Roan <alr16@aber.ac.uk>
 * Master class. All all classes in database controller extend this class
 */
class API{
    
    var $connection = null;
    
    /**
     * Constructor
     */
    function __construct() {
        $this->ConnectToDatabase();
    }   
    
    /**
     * Constructor
     */
    function API(){
        $this->__construct();
    }
    
    /**
     * Connects service to database
     */
    function ConnectToDatabase(){
        if (is_null($this->connection)){
            $this->connection = pg_connect("<INPUT PARAMS>");           
        }
    }
    
    /**
     * Closes the current connection to the database
     */
    function CloseConnection(){
        if($this->connection){
            pg_close($this->connection);
        }
    }
    
    /**
     * Retrieve a variable from the object
     * @param String $var_name
     * @return Varying Returned value of variable of name $varname
     */
    function Get($var_name = ''){
        if(isset($this->$var_name)){
            return $this->$var_name;
        }
        else{
            return false;
        }
    }
    
    /**
     * Set value of variable
     * @param String $var_name
     * @param Varying $value
     * @return boolean success
     */
    function Set($var_name = '', $value = ''){
        if(isset($this->$var_name)){
            $this->$var_name = $value;
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * Retrieves all attributes in object except database connection
     * @return Array all atributes associated with current object except connection to database
     */
    function GetAll(){
        $object_vars = get_object_vars($this);        
        unset($object_vars['connection']);
        return $object_vars;
    }
    
}

?>
