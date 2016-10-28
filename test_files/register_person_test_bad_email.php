<?php 

$data = array("request" => "register_person",
	"email" => "alex", 
        "first_name" => "Alex",
        "last_name" => "Roan",
        "password" => "password");
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
     $json_result = json_decode($result, true);
    
    if($json_result['success']){
        print_r("test failed");
        print_r($json_result['details']);
    }
    else{
        print_r("test passed\n\n");
        print_r($json_result['details']);
    }
    
?>
