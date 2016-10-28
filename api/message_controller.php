<?php
require 'api.php';
require 'message.php';

/**
 * Controller class to the Message model class
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Message_Controller extends API{
    
    var $message = null;
    var $messages = null;
    
    /**
     * Constructor.
     */
    function __construct(){
        $this->ConnectToDatabase();
    }
    
    /**
     * Constructor.
     */
    function Message_Controller(){
        $this->__construct();
    }
    
    /**
     * Inserts a new message into the database using message->Create()
     * @param String $ps_email_sender
     * @param String $ps_email_receiver
     * @param String $table_ref
     * @param String $table_ref_pk
     * @param String $title
     * @param String $body
     * @return boolean success
     */
    function SendMessage($ps_email_sender = "", $ps_email_receiver = "", $table_ref = "", $table_ref_pk = "", $title = "",$body = ""){
        $returned = false;
        if($ps_email_sender != "" && $ps_email_receiver != ""){
            
            $this->message = new Message();
            $this->message->Set("ms_ps_sender", $ps_email_sender);
            $this->message->Set("ms_ps_receiver", $ps_email_receiver);
            $this->message->Set("ms_table_ref", $table_ref);
            $this->message->Set("ms_table_ref_pk", $table_ref_pk);
            $this->message->Set("ms_title", $title);
            $this->message->Set("ms_body", $body);
            $returned = $this->message->Create();
        }
        return $returned;
    }
    
    /**
     * Loads a message to $this->message
     * @param Integer $ms_pk
     * @return boolean success
     */
    function LoadMessage($ms_pk = ""){
        $returned = false;
        $this->message = new Message($ms_pk);
        if($this->message){
            $returned = $this->message;            
        }
        return $returned;
    }
    
    /**
     * Loads all messages to $this->messages
     * @param String $ps_email
     * @param String $inbox_or_sent "i" or "s"
     * @return boolean success
     */
    function LoadMyMessages($ps_email = "", $inbox_or_sent = ""){
        $returned = false;
        if(($ps_email != "") && ($inbox_or_sent != "")){
            if($inbox_or_sent == "i"){
                $sql_string = "select * from message where ms_ps_receiver = '$ps_email' order by ms_sent_date desc";
            }
            else if ($inbox_or_sent == "s"){
                $sql_string = "select * from message where ms_ps_sender = '$ps_email' order by ms_sent_date desc";
            }
            else{
                return FALSE;
            }            
            $response = pg_query($sql_string);
            if($response){
                $num_rows = pg_num_rows($response);
                $this->messages = Array();
                if($num_rows > 1){                    
                    $response_array = pg_fetch_all($response);
                    for($i = 0; $i < $num_rows; $i++){
                        $ms_pk = $response_array[$i]['ms_pk'];
                        $message = new Message($ms_pk);
                        array_push($this->messages, $message);
                    }
                }
                else if ($num_rows == 1){
                    $response_array = pg_fetch_assoc($response);
                    $ms_pk = $response_array['ms_pk'];
                    $message = new Message($ms_pk);
                    array_push($this->messages, $message);
                }
                $returned = true;
            }
        }
        return $returned;
    }
    
    /**
     * Returns $this->messages
     * @return array messages
     */
    function GetMessages(){
        $returned = false;
        if($this->messages){
            $returned = Array();
            for($i=0; $i<count($this->messages); $i++){
                $message_details = $this->messages[$i]->GetAll();
                array_push($returned, $message_details);
            }
        }
        return $returned;
    }
    
    /**
     * @return integer nubmer of unread messages
     */
    function GetNumberOfUnreadMessages(){
        $returned = false;
        $count = 0;
        if($this->messages){
            for($i=0; $i<count($this->messages); $i++){
                $this_message = $this->messages[$i];
                if($this_message->Get("ms_read_date") == ""){
                    $count++;
                }
            }
            $returned = $count;
        }
        return $returned;
    }
    
}

?>
