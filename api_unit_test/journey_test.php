<?php

require '../api/journey.php';


class journey_test extends PHPUnit_Framework_TestCase {
   
    
    public function test_create_load_update_delete_journey(){
        
        $jr_ps_email = "alex.roan@hotmail.com";
        $jr_origin = "Glasgow";
        $jr_destination = "London";
        $jr_etd = "2014-05-05 00:00:00";
        $jr_eta = "2014-05-06 00:00:00";
        $jr_spaces_available = "3";
        $jr_total_spaces = "3";
        $jr_description = "PART OF A UNIT TEST";
        $jr_origin_latitude = "51";
        $jr_origin_longitude = "0";
        $jr_destination_latitude = "51";
        $jr_destination_longitude = "3";
        $jr_total_distance = "600";
        
        $journey = new Journey();
        $journey->Set("jr_ps_email", $jr_ps_email);
        $journey->Set("jr_origin", $jr_origin);
        $journey->Set("jr_destination", $jr_destination);
        $journey->Set("jr_etd", $jr_etd);
        $journey->Set("jr_eta", $jr_eta);
        $journey->Set("jr_spaces_available", $jr_spaces_available);
        $journey->Set("jr_total_spaces", $jr_total_spaces);
        $journey->Set("jr_description", $jr_description);
        $journey->Set("jr_origin_latitude", $jr_origin_latitude);
        $journey->Set("jr_origin_longitude", $jr_origin_longitude);
        $journey->Set("jr_destination_latitude", $jr_destination_latitude);
        $journey->Set("jr_destination_longitude", $jr_destination_longitude);
        $journey->Set("jr_total_distance", $jr_total_distance);
        
        $get_jr_ps_email = $journey->Get("jr_ps_email");
        $get_jr_origin = $journey->Get("jr_origin");
        $get_jr_destination = $journey->Get("jr_destination");
        $get_jr_etd = $journey->Get("jr_etd");
        $get_jr_eta = $journey->Get("jr_eta");
        $get_jr_spaces_available = $journey->Get("jr_spaces_available");
        $get_jr_total_spaces = $journey->Get("jr_total_spaces");
        $get_jr_description = $journey->Get("jr_description");
        $get_jr_origin_latitude = $journey->Get("jr_origin_latitude");
        $get_jr_origin_longitude = $journey->Get("jr_origin_longitude");
        $get_jr_destination_latitude = $journey->Get("jr_destination_latitude");
        $get_jr_destination_longitude = $journey->Get("jr_destination_longitude");
        $get_jr_total_distance = $journey->Get("jr_total_distance");
        
        $this->assertEquals($jr_ps_email, $get_jr_ps_email);
        $this->assertEquals($jr_origin, $get_jr_origin);
        $this->assertEquals($jr_destination, $get_jr_destination);
        $this->assertEquals($jr_etd, $get_jr_etd);
        $this->assertEquals($jr_eta, $get_jr_eta);
        $this->assertEquals($jr_spaces_available, $get_jr_spaces_available);
        $this->assertEquals($jr_total_spaces, $get_jr_total_spaces);
        $this->assertEquals($jr_description, $get_jr_description);
        $this->assertEquals($jr_origin_latitude, $get_jr_origin_latitude);
        $this->assertEquals($jr_origin_longitude, $get_jr_origin_longitude);
        $this->assertEquals($jr_destination_latitude, $get_jr_destination_latitude);
        $this->assertEquals($jr_destination_longitude, $get_jr_destination_longitude);
        $this->assertEquals($jr_total_distance, $get_jr_total_distance);
        
        $create = $journey->create();
        $this->assertTrue($create);
        
        $jr_pk = $journey->Get("jr_pk");
        
        unset($journey);
        $journey = new Journey($jr_pk);
        
        $this->assertEquals($jr_ps_email, $journey->Get("jr_ps_email"));
        $this->assertEquals($jr_origin, $journey->Get("jr_origin"));
        $this->assertEquals($jr_destination, $journey->Get("jr_destination"));
        $this->assertEquals($jr_etd, $journey->Get("jr_etd"));
        $this->assertEquals($jr_eta, $journey->Get("jr_eta"));
        $this->assertEquals($jr_spaces_available, $journey->Get("jr_spaces_available"));
        $this->assertEquals($jr_total_spaces, $journey->Get("jr_total_spaces"));
        $this->assertEquals($jr_description, $journey->Get("jr_description"));
        $this->assertEquals($jr_origin_latitude, $journey->Get("jr_origin_latitude"));
        $this->assertEquals($jr_origin_longitude, $journey->Get("jr_origin_longitude"));
        $this->assertEquals($jr_destination_latitude, $journey->Get("jr_destination_latitude"));
        $this->assertEquals($jr_destination_longitude, $journey->Get("jr_destination_longitude"));
        $this->assertEquals($jr_total_distance, $journey->Get("jr_total_distance"));
        
        $this->assertEquals($journey->Get("jr_origin_latitude"), $jr_origin_latitude);
        $jr_origin_latitude = 987;
        $journey->Set("jr_origin_latitude", $jr_origin_latitude);
        $update = $journey->Update();
        $this->assertTrue($update);       
        
        $delete = $journey->Delete();
        $this->assertTrue($delete);
    }
    
}

?>
