<?php

require '../api/hitch_request.php';
class hitch_request_test extends PHPUnit_Framework_TestCase{   
    
    public function test_create_load_update_delete_hitch_request(){
        $hr_jr = 24;
        $hr_ps_email = "alex.roan@hotmail.com";
        $hr_waypoint_1 = "";
        
        $hitch_request = new Hitch_Request();
        $hitch_request->Set("hr_jr", $hr_jr);
        $hitch_request->Set("hr_ps_email", $hr_ps_email);
        
        $get_hr_jr = $hitch_request->Get("hr_jr");
        $get_hr_ps_email = $hitch_request->Get("hr_ps_email");
        
        $this->assertEquals($hr_jr, $get_hr_jr);
        $this->assertEquals($hr_ps_email, $get_hr_ps_email);
        
        $create =  $hitch_request->Create();
        $this->assertTrue($create);
        
        $hr_pk = $hitch_request->Get("hr_pk");
        
        unset($hitch_request);
        $hitch_request = new Hitch_Request($hr_pk);
        $this->assertEquals($hr_jr, $hitch_request->Get("hr_jr"));
        $this->assertEquals($hr_ps_email, $hitch_request->Get("hr_ps_email"));
        
        $this->assertEquals($hitch_request->Get("hr_waypoint_1"), $hr_waypoint_1);
        $hr_waypoint_1 = "Poole";
        $hitch_request->Set("hr_waypoint_1", $hr_waypoint_1);
        $update = $hitch_request->Update();
        $this->assertTrue($update);       
        
        $delete = $hitch_request->Delete();
        $this->assertTrue($delete);
    }
    
    
}

?>
