<?php
require '../api/hitch_request_controller.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hitch_request_controller_test
 *
 * @author alr16
 */
class hitch_request_controller_test extends PHPUnit_Framework_TestCase{
    //put your code here
    
    public function test_GetHitchRequestForJourney(){
        $jr_pk = 24;
        $hr_control = new Hitch_Request_Controller();
        $results = $hr_control->GetHitchRequestsForJourney($jr_pk);
        $test_jr_pk = $results[0][0];
        $this->assertEquals($jr_pk, $test_jr_pk);
    }
    
    public function test_Load(){
        $ps_email = "alex.roan@hotmail.com";
        $hr_control = new Hitch_Request_Controller();
        
    }
}

?>
