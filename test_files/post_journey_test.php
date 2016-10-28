<?php 

    $data = array("jr_ps_email" => "alex.roan@hotmail.com", 
        "jr_origin" => "ABERYSTWYTH, UK",
        "jr_destination" => "PENARTH, UK",
        "jr_etd" => "2014-03-15 11:00",
        "jr_eta" => "2014-03-15 16:30",
        "jr_spaces_available" => 3,
        "jr_total_spaces" => 3,
        "jr_description" => "ABER - PENARTH",
        "jr_extra_distance" => 35.0,
        "waypoints" => "");
        
    $data_string = json_encode($data);
    
    $ch = curl_init('http://users.aber.ac.uk/alr16/major_project/journey/add.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($data_string))                                                                       
    );                                                                                                                   

    $result = curl_exec($ch);    
    print_r($result);
  

?>
