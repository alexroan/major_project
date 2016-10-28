<?php

    $data = array("request" => "decline_hitch_request", 
        "hr_pk" => 12);
        
    $data_string = json_encode($data);
    
    $ch = curl_init('http://users.aber.ac.uk/alr16/major_project/database_files/index.php');
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
