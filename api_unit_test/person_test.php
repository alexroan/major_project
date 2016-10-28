<?php

require '../api/person.php';

class Person_Test extends PHPUnit_Framework_TestCase{
    
    var $ps_email = "abc@123.com";
    var $ps_first_name = "ABC";
    var $ps_last_name = "DEF";
    var $ps_password = "PASS";
    var $ps_home_1 = "Birmingham";
    var $ps_home_2 = "";
    var $ps_frequent_destination_1 = "London";
    var $ps_frequent_destination_2 = "Glasgow";
    var $ps_frequent_destination_3 = "Aberystwyth";
    var $ps_current_location = "Birmingham";
    
    /**
     * test creating a new person
     */
    public function test_create_person(){    
        $person = new Person();
        $person->Set("ps_email", $this->ps_email);
        $person->Set("ps_first_name", $this->ps_first_name);
        $person->Set("ps_last_name", $this->ps_last_name);
        $person->Set("ps_password", $this->ps_password);
        $person->Set("ps_home_1", $this->ps_home_1);
        $person->Set("ps_home_2", $this->ps_home_2);
        $person->Set("ps_frequent_destination_1", $this->ps_frequent_destination_1);
        $person->Set("ps_frequent_destination_2", $this->ps_frequent_destination_2);
        $person->Set("ps_frequent_destination_3", $this->ps_frequent_destination_3);
        $person->Set("ps_current_location", $this->ps_current_location);
        
        $get_ps_email = $person->Get("ps_email");
        $get_ps_first_name = $person->Get("ps_first_name");
        $get_ps_last_name = $person->Get("ps_last_name");
        $get_ps_password = $person->Get("ps_password");
        $get_ps_home_1 = $person->Get("ps_home_1");
        $get_ps_home_2 = $person->Get("ps_home_2");
        $get_ps_frequent_destination_1 = $person->Get("ps_frequent_destination_1");
        $get_ps_frequent_destination_2 = $person->Get("ps_frequent_destination_2");
        $get_ps_frequent_destination_3 = $person->Get("ps_frequent_destination_3");
        $get_ps_current_location = $person->Get("ps_current_location");
        
        $this->assertEquals($this->ps_email, $get_ps_email);
        $this->assertEquals($this->ps_first_name, $get_ps_first_name);
        $this->assertEquals($this->ps_last_name, $get_ps_last_name);
        $this->assertEquals($this->ps_password, $get_ps_password);
        $this->assertEquals($this->ps_home_1, $get_ps_home_1);
        $this->assertEquals($this->ps_home_2, $get_ps_home_2);
        $this->assertEquals($this->ps_frequent_destination_1, $get_ps_frequent_destination_1);
        $this->assertEquals($this->ps_frequent_destination_2, $get_ps_frequent_destination_2);
        $this->assertEquals($this->ps_frequent_destination_3, $get_ps_frequent_destination_3);
        $this->assertEquals($this->ps_current_location, $get_ps_current_location);
        
        $create = $person->Create();
        $this->assertTrue($create);
    }
    
    /**
     * Test that the person object loads the correct email address
     */
    public function test_load_person(){        
        $person = new Person($this->ps_email);
        $this->assertEquals($this->ps_email, $person->Get("ps_email"));
        $this->assertEquals($this->ps_first_name, $person->Get("ps_first_name"));
        $this->assertEquals($this->ps_last_name, $person->Get("ps_last_name"));
        $this->assertEquals($this->ps_password, $person->Get("ps_password"));
        $this->assertEquals($this->ps_home_1, $person->Get("ps_home_1"));
        $this->assertEquals($this->ps_home_2, $person->Get("ps_home_2"));
        $this->assertEquals($this->ps_frequent_destination_1, $person->Get("ps_frequent_destination_1"));
        $this->assertEquals($this->ps_frequent_destination_2, $person->Get("ps_frequent_destination_2"));
        $this->assertEquals($this->ps_frequent_destination_3, $person->Get("ps_frequent_destination_3"));
        $this->assertEquals($this->ps_current_location, $person->Get("ps_current_location"));
    }
    
    /**
     * test updating a person's details
     */
    public function test_update_person(){        
        $person = new Person($this->ps_email);
        $this->assertEquals($person->Get("ps_home_2"), $this->ps_home_2);
        $this->ps_home_2 = "Cardiff";
        $this->assertNotEquals($person->Get("ps_home_2"), $this->ps_home_2);
        $person->Set("ps_home_2", $this->ps_home_2);
        $this->assertEquals($person->Get("ps_home_2"), $this->ps_home_2);
        $update = $person->Update();
        $this->assertTrue($update);
    }
    
    /**
     * test deleting a person from the database
     */
    public function test_delete_person(){
        $person = new Person($this->ps_email);
        $delete = $person->Delete();
        $this->assertTrue($delete);
    }
    
}




?>
