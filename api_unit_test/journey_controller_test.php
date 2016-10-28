<?php
require '../api/journey_controller.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of journey_controller_test
 *
 * @author alr16
 */
class journey_controller_test extends PHPUnit_Framework_TestCase{
    //put your code here
    
    public function test_GetMyJourneys(){
        $j_control = new Journey_Controller();
        $ps_email = "alex.roan@hotmail.com";
        $journeys = $j_control->GetMyJourneys($ps_email);
        $test_ps_email = $journeys[0]['jr_ps_email'];
        $this->assertEquals($ps_email, $test_ps_email);
    }    
    
    public function test_LoadJourney_GetJourneyDataAll(){
        $jr_pk = 43;
        $j_control = new Journey_Controller();
        $returned = $j_control->LoadJourney($jr_pk);
        $this->assertTrue($returned);
        $journey = $j_control->GetJourneyDataAll();
        $test_jr_pk = $journey['jr_pk'];
        $this->assertEquals($jr_pk, $test_jr_pk);
    }
    
}

?>
