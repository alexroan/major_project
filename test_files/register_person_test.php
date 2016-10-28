<?php 

    
    $data = array("ps_email" => "ecg1@aber.ac.uk", 
        "ps_first_name" => "Caitlin",
        "ps_last_name" => "Griffiths",
        "ps_password" => "password",
        "ps_current_location" => "ABERYSTWYTH, UK",
        "ps_home_1" => "ABERYSTWYTH, UK",
        "ps_home_2" => "CARDIFF, UK",
        "ps_frequent_destination_1" => "CARDIFF, UK",
        "ps_frequent_destination_2" => "BRIDGEND, UK",
        "ps_frequent_destination_3" => "BRISTOL, UK");
    $data_string = json_encode($data);
    
    $ch = curl_init('http://users.aber.ac.uk/alr16/major_project/person/add.php');
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
