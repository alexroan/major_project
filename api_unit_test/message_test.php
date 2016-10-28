<?php

require '../api/message.php';

class message_test extends PHPUnit_Framework_TestCase{    
    
    
    /**
     * test creating a message
     */
    public function test_create_load_update_delete_message(){   
            
        $ms_ps_sender = "alex.roan@hotmail.com";
        $ms_ps_receiver = "ALR16@ABER.AC.UK";
        $ms_title = "TEST MESSAGE";
        $ms_body = "This message is part of a unit test";
        $ms_read_date = null;
        
        $message = new Message();
        $message->Set("ms_ps_sender", $ms_ps_sender);
        $message->Set("ms_ps_receiver", $ms_ps_receiver);
        $message->Set("ms_title", $ms_title);
        $message->Set("ms_body", $ms_body);
        
        $get_ms_ps_sender = $message->Get("ms_ps_sender");
        $get_ms_ps_receiver = $message->Get("ms_ps_receiver");
        $get_ms_title = $message->Get("ms_title");
        $get_ms_body = $message->Get("ms_body");
        
        $this->assertEquals($ms_ps_sender, $get_ms_ps_sender);
        $this->assertEquals($ms_ps_receiver, $get_ms_ps_receiver);
        $this->assertEquals($ms_title, $get_ms_title);
        $this->assertEquals($ms_body, $get_ms_body);
        
        $create = $message->Create();
        $this->assertTrue($create);
        
        $ms_pk = $message->Get("ms_pk");
       
        unset($message);
        $message = new Message($ms_pk);
        $this->assertEquals($ms_ps_sender, $message->Get("ms_ps_sender"));
        $this->assertEquals($ms_ps_receiver, $message->Get("ms_ps_receiver"));
        $this->assertEquals($ms_title, $message->Get("ms_title"));
        $this->assertEquals($ms_body, $message->Get("ms_body"));
        
        
        $this->assertEquals($message->Get("ms_read_date"), $ms_read_date);
        $ms_read_date = date('Y-m-d H:i:s');
        $message->Set("ms_read_date", $ms_read_date);
        $update = $message->Update();
        $this->assertTrue($update);       
        
        $delete = $message->Delete();
        $this->assertTrue($delete);
        
    }
    
    
}


?>
