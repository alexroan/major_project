<?php 

    /**
     * Encodes a response without any details
     * @param string $request request type that the server is responding to 
     * @param boolean $success true or false depending on success
     */
    /*function encode_post($request, $success){
        $response_data = array("request" => $request,
            "success" => $success);
        $data_string = json_encode($response_data);
        echo $data_string;
    }*/
    
    /**
     * Encodes response with details
     * @param string $request request type that the server is responding to 
     * @param boolean $success true or false depending on success
     * @param string $details details about the success/failure of the request
     */
    /*function encode_post_details($request, $success, $details){
        $response_data = array("request" => $request,
            "success" => $success,
            "details" => $details);
        $data_string = json_encode($response_data);
        echo $data_string;
    }*/
    
    
    function encode_json($request_type, $response){
        $response_data = array("request" => $request_type,
            "details" => $response);
        $data_string = json_encode($response_data);
        echo $data_string;
    }
    


?>
