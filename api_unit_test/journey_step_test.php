<?php
require '../api/journey_step.php';

class journey_step_test extends PHPUnit_Framework_TestCase{
    
    
    public function test_create_load_update_delete_journey_step(){
        $js_pk = null;
        
        $js_jr = "24";
        $js_step_order = "0";
        $js_latitude = "123";
        $js_longitude = "456";
        
        $journey_step = new Journey_Step();
        $journey_step->Set("js_jr", $js_jr);
        $journey_step->Set("js_step_order", $js_step_order);
        $journey_step->Set("js_latitude", $js_latitude);
        $journey_step->Set("js_longitude", $js_longitude);
        
        $get_js_jr = $journey_step->Get("js_jr");
        $get_js_step_order = $journey_step->Get("js_step_order");
        $get_js_latitude = $journey_step->Get("js_latitude");
        $get_js_longitude = $journey_step->Get("js_longitude");
        
        $this->assertEquals($js_jr, $get_js_jr);
        $this->assertEquals($js_step_order, $get_js_step_order);
        $this->assertEquals($js_latitude, $get_js_latitude);
        $this->assertEquals($js_longitude, $get_js_longitude);
        
        $create =  $journey_step->Create();
        $this->assertTrue($create);
        
        $js_pk = $journey_step->Get("js_pk");
        
        unset($journey_step);
        $journey_step = new Journey_Step($js_pk);
        $this->assertEquals($js_jr, $journey_step->Get("js_jr"));
        $this->assertEquals($js_step_order, $journey_step->Get("js_step_order"));
        $this->assertEquals($js_latitude, $journey_step->Get("js_latitude"));
        $this->assertEquals($js_longitude, $journey_step->Get("js_longitude"));
        
        
        $this->assertEquals($journey_step->Get("js_latitude"), $js_latitude);
        $js_latitude = 987;
        $journey_step->Set("js_latitude", $js_latitude);
        $update = $journey_step->Update();
        $this->assertTrue($update);       
        
        $delete = $journey_step->Delete();
        $this->assertTrue($delete);
    }
    
    
}

?>
