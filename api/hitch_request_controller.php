<?php

require_once 'api.php';
require_once 'hitch_request.php';
require_once 'journey_controller.php';
require_once 'journey_step_controller.php';

/**
 * @author Alex Roan <alr16@aber.ac.uk>
 * Controller to the Hitch_Request model class
 */
class Hitch_Request_Controller extends API{
    
    var $hitch_request = null;   
    var $journey_controller = null;
    var $journey_step_controller = null;
    
    var $search_results = null;
    
    /**
     * Constructor, calls LoadHitchRequest() if $hr_pk != ""
     * @param Integer $hr_pk
     */
    function __construct($hr_pk = ""){
        $this->ConnectToDatabase();
        $this->hitch_request = new Hitch_Request();
        $this->journey_controller = new Journey_Controller();
        $this->journey_step_controller = new Journey_Step_Controller();
        if($hr_pk != ""){
            $this->LoadHitchRequest($hr_pk);
        }
    }
    
    /**
     * Constructor, calls LoadHitchRequest() if $hr_pk != ""
     * @param Integer $hr_pk
     */
    function Hitch_Request_Controller($hr_pk = ""){
        $this->__construct($hr_pk);
    }
    
    /**
     * Gets all hitch requests from hitch_request table for journey specified
     * by $jr_pk parameter
     * @param Integer $jr_pk
     * @return Array array of hitch_request->GetAll() for each hitch request
     */
    function GetHitchRequestsForJourney($jr_pk = ""){
        $returned = null;
        $this->search_results = Array();
        if($jr_pk != ""){
            $sql_string = "select * from hitch_request where hr_jr = $jr_pk";
            $response = pg_query($sql_string);
            if($response){
                $num_rows = pg_num_rows($response);
                if($num_rows > 1){
                    $search_array = pg_fetch_all($response);
                    for($i=0; $i<$num_rows; $i++){
                        $hr_pk = $search_array[$i]['hr_pk'];
                        $hitch_request = new Hitch_Request($hr_pk);
                        array_push($this->search_results, $hitch_request);
                    }
                }
                else if ($num_rows == 1){
                    $search_array = pg_fetch_assoc($response);
                    $hr_pk = $search_array['hr_pk'];
                    $hitch_request = new Hitch_Request($hr_pk);
                    array_push($this->search_results, $hitch_request);
                }
                $returned = $this->ReturnHitchRequests();
            }
        }
        return $returned;
    }
    
    /**
     * returns new hitch requests for a particular person
     * @param String $ps_email
     * @return Array array of GetAll() data for each new hitch request
     */
    function GetNewRequests($ps_email = ""){
        $returned = null;
        $this->search_results = Array();
        if($ps_email != ""){
            $my_journeys = $this->journey_controller->GetMyJourneys($ps_email);
            if($my_journeys){
                $sql_string = "select * from hitch_request where hr_jr in (";
                for($i=0; $i<count($my_journeys); $i++){
                    $jr_pk = $my_journeys[$i]['jr_pk'];
                    $sql_string = $sql_string."$jr_pk,";
                }                
                $sql_string = rtrim($sql_string, ",");
                $sql_string = $sql_string.") and hr_response is null";
                $response = pg_query($sql_string);
                $num_rows = pg_num_rows($response);
                if($num_rows > 1){
                    $rows = pg_fetch_all($response);
                    for($i=0; $i<$num_rows; $i++){
                        $hr_pk = $rows[$i]['hr_pk'];
                        $hitch_request = new Hitch_Request($hr_pk);
                        array_push($this->search_results, $hitch_request);
                    }                    
                }
                else if($num_rows == 1){
                    $row = pg_fetch_assoc($response);
                    $hr_pk = $row['hr_pk'];
                    $hitch_request = new Hitch_Request($hr_pk);
                    array_push($this->search_results, $hitch_request);
                }
                $returned = $this->ReturnHitchRequests();
            }
        }
        return $returned;
    }
    
    /**
     * Returns hitch requests made by a particular person
     * @param String $ps_email
     * @return Array array of GetAll() data for each hitch request
     */
    function GetMyHitchRequests($ps_email = ""){
        $returned = null;
        $this->search_results = Array();
        if($ps_email != ""){
            $sql_string = "select hr.hr_pk from hitch_request hr join journey jr 
                on jr.jr_pk = hr.hr_jr where hr.hr_ps_email = '$ps_email' and jr.jr_etd >= Now() order by jr.jr_etd";
            $response = pg_query($sql_string);
            $num_rows = pg_num_rows($response);
            if($num_rows > 1){
                $rows = pg_fetch_all($response);
                for($i=0; $i<$num_rows; $i++){
                    $hr_pk = $rows[$i]['hr_pk'];
                    $hitch_request = new Hitch_Request($hr_pk);
                    array_push($this->search_results, $hitch_request);
                }                
            }
            else if($num_rows == 1){
                $row = pg_fetch_assoc($response);
                $hr_pk = $row['hr_pk'];
                $hitch_request = new Hitch_Request($hr_pk);
                array_push($this->search_results, $hitch_request);
            }
            $returned = $this->ReturnHitchRequests();
        }
        return $returned;
    }
    
    /**
     * Formats the result into an array of GetAll data to be parsed to website
     * @return Array array of GetAll() data for each result
     */
    private function ReturnHitchRequests(){
        $returned = null;       
        if($this->search_results){
            $returned = Array();
            for($i=0; $i<count($this->search_results); $i++){
                $hr_jr_array = Array();
                $hr = $this->search_results[$i];
                $this->journey_controller->LoadJourney($hr->Get("hr_jr"));
                $journey_data = $this->journey_controller->GetJourneyDataAll();
                $hr_data = $hr->GetAll();
                foreach($journey_data as $key => $value){
                    array_push($hr_jr_array, $value);
                }
                foreach($hr_data as $key => $value){
                    array_push($hr_jr_array, $value);
                }
                array_push($returned, $hr_jr_array);                
            }            
        }
        return $returned;
    }
    
    /**
     * Creates a Hitch_Request object and calls Create() to insert into database
     * @param Integer $jr_pk
     * @param String $ps_email
     * @param String $hr_waypoint_1
     * @param String $hr_waypoint_2
     * @return Boolean success
     */
    function CreateHitchRequest($jr_pk, $ps_email, $hr_waypoint_1 = "", $hr_waypoint_2 = ""){
        $returned = false;
        if($jr_pk && $ps_email){
            $this->hitch_request = new Hitch_Request();
            $this->hitch_request->Set("hr_jr", $jr_pk);
            $this->hitch_request->Set("hr_ps_email", $ps_email);
            $this->hitch_request->Set("hr_waypoint_1", $hr_waypoint_1);
            $this->hitch_request->Set("hr_waypoint_2", $hr_waypoint_2);
            $returned = $this->hitch_request->Create();
        }
        return $returned;
    }
    
    /**
     * Loads particular Hitch Request
     * @param Integer $hr_pk
     * @return boolean success
     */
    function LoadHitchRequest($hr_pk = ""){
        $returned = false;    
        if($hr_pk != ""){
            $this->hitch_request->Load($hr_pk);
            if($this->hitch_request->Get("hr_pk") == $hr_pk){
                $returned = true;
            }
        }
        return $returned;
    }
    
    /**
     * Accepts hitch request stored in $this->hitch_request attribute
     * @return boolean success
     */
    function AcceptHitchRequest(){
        $returned = false;
        //Make sure hitch_request has loaded
        if(!is_null($this->hitch_request)){
            if($this->hitch_request->Get("hr_response") == ""){
                $this->search_results = Array();
                $jr_pk = $this->hitch_request->Get("hr_jr");
                if($jr_pk){
                    //get all accepted hitch_requests related to this journey
                    //push into $this->search_results
                    $sql_string = "select * from hitch_request where hr_jr = $jr_pk and hr_response = 1";
                    $response = pg_query($sql_string);
                    $num_rows = pg_num_rows($response);
                    if($num_rows > 1){
                        $response_array = pg_fetch_all($response);
                        for($i=0; $i<$num_rows; $i++){
                            $hitch_request = new Hitch_Request($response_array[$i]["hr_pk"]);
                            array_push($this->search_results, $hitch_request);
                        }
                    }
                    else if($num_rows == 1){
                        $response_array = pg_fetch_assoc($sql_string);
                        $hitch_request = new Hitch_Request($response_array["hr_pk"]);
                        array_push($this->search_results, $hitch_request);
                    }

                    $waypoint_array = Array();
                    //push waypoints from hitch request in question to waypoint_array
                    if($this->hitch_request->Get("hr_waypoint_1") != ""){
                        array_push($waypoint_array, $this->hitch_request->Get("hr_waypoint_1"));
                    }
                    if($this->hitch_request->Get("hr_waypoint_2") != ""){
                        array_push($waypoint_array, $this->hitch_request->Get("hr_waypoint_2"));
                    }
                    //push waypoints from existing accepted hitch_requests to waypoint_array
                    for($i=0; $i<count($this->search_results); $i++){
                        $this_result = $this->search_results[$i];
                        if(($this_result->Get("hr_waypoint_1") != "") && (!in_array($this_result->Get("hr_waypoint_1"), $waypoint_array))){
                            array_push($waypoint_array, $this_result->Get("hr_waypoint_1"));
                        }
                        if(($this_result->Get("hr_waypoint_2") != "") && (!in_array($this_result->Get("hr_waypoint_2"), $waypoint_array))){
                            array_push($waypoint_array, $this_result->Get("hr_waypoint_2"));
                        }
                    }       
                    $this->journey_controller->LoadJourney($jr_pk);
                    if($this->journey_controller->journey->Get("jr_spaces_available") > 0){
                        $this->journey_controller->SetWaypointArray($waypoint_array);
                        $success = $this->journey_controller->ModifyJourneySteps();
                        if($success){
                            $this->hitch_request->Set("hr_response", 1);
                            $this->hitch_request->Set("hr_response_date", date('Y-m-d H:i:s'));
                            $returned = $this->hitch_request->Update();
                        }
                    }
                }
            }
        }
        return $returned;
    }
    
    /**
     * Declines hitch_request stored in $this->hitch_request
     * @return boolean success
     */
    function DeclineHitchRequest(){
        $returned = false;
        if(isset($this->hitch_request)){
            $this->hitch_request->Set("hr_response", 0);
            $this->hitch_request->Set("hr_response_date", date('Y-m-d H:i:s'));
            $returned = $this->hitch_request->Update();            
        }    
        return $returned;
    }
    
}

?>
