<?php
require_once 'api.php';
require_once 'journey.php';
require_once 'journey_step_controller.php';

/**
 * Controller for Journey model class
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Journey_Controller extends API{
    
    var $jr_ps_email;
    var $jr_origin;
    var $jr_destination;
    var $waypoints;
    var $waypoint_array;
    var $jr_etd;
    var $jr_eta;
    var $jr_total_spaces;
    var $jr_spaces_available;
    
    var $jr_origin_lat = null;
    var $jr_origin_lng = null;
    var $jr_destination_lat = null;
    var $jr_destination_lng = null;
    var $search_date_1 = "";
    var $search_date_2 = "";
    var $search_results = null;
    
    var $google_directions_url = "";
    var $google_directions_data = null;
    var $journey = null;
    var $journeys = null;
    
    var $journey_step_controller = null;
    
    /**
     * Constructor, sets attributes if not = ""
     * @param String $jr_ps_email
     * @param String $jr_origin
     * @param String $jr_destination
     * @param Array $waypoints
     * @param DateTime $jr_etd
     * @param DateTime $jr_eta
     * @param Integer $jr_total_spaces
     * @param Integer $jr_spaces_available
     */
    function __construct($jr_ps_email = "", $jr_origin = "", $jr_destination = "", $waypoints = "", $jr_etd = "", $jr_eta = "", $jr_total_spaces = "", $jr_spaces_available = ""){
        $this->ConnectToDatabase();
        $this->journey_step_controller = new Journey_Step_Controller();
        $this->jr_ps_email = $jr_ps_email;
        $this->jr_origin = $jr_origin;
        $this->jr_destination = $jr_destination;
        $this->waypoints = $waypoints;
        $this->jr_etd = $jr_etd;
        $this->jr_eta = $jr_eta;
        $this->jr_total_spaces = $jr_total_spaces;
        $this->jr_spaces_available = $jr_spaces_available;
    }
    
    /**
     * Constructor, sets attributes if not = ""
     * @param String $jr_ps_email
     * @param String $jr_origin
     * @param String $jr_destination
     * @param Array $waypoints
     * @param DateTime $jr_etd
     * @param DateTime $jr_eta
     * @param Integer $jr_total_spaces
     * @param Integer $jr_spaces_available
     */
    function Journey_Controller($jr_ps_email = "", $jr_origin = "", $jr_destination = "", $waypoints = "", $jr_etd = "", $jr_eta = "", $jr_total_spaces = "", $jr_spaces_available = ""){
        $this->__construct($jr_ps_email, $jr_origin, $jr_destination, $waypoints, $jr_etd, $jr_eta, $jr_total_spaces, $jr_spaces_available);        
    }
    
    /**
     * Returns journeys posted by a particular person
     * @param String $ps_email
     * @return array journey details arrays
     */
    function GetMyJourneys($ps_email = ""){ 
        $returned = null;
        $this->search_results = Array();
        if($ps_email != ""){
            $sql_string = "select jr_pk from journey where jr_ps_email = '$ps_email' and jr_is_cancelled = 0 and jr_etd >= Now() order by jr_etd";
            $response = pg_query($sql_string);
            $num_rows = pg_num_rows($response);
            if($num_rows > 1){
                $rows = pg_fetch_all($response);
                for($i = 0; $i < $num_rows; $i++){
                    $jr_pk = $rows[$i]['jr_pk'];
                    $journey = new Journey($jr_pk);
                    array_push($this->search_results, $journey);
                }
                $returned = $this->ReturnJourneys();
            }
            else if ($num_rows == 1){
                $row = pg_fetch_assoc($response);
                $jr_pk = $row['jr_pk'];
                $journey = new Journey($jr_pk);
                array_push($this->search_results, $journey);
                $returned = $this->ReturnJourneys();
            }            
        }
        return $returned;
    }
    
    /**
     * Returns journey array formatted to parse to website
     * @return array array of GetAll() arrays for each journey
     */
    private function ReturnJourneys(){
        $returned = null;
        if($this->search_results){
            $returned = Array();
            for($i=0; $i<count($this->search_results); $i++){
                array_push($returned, $this->search_results[$i]->GetAll());                
            }            
        }
        return $returned;
    }
    
    /**
     * decrements the number of remaining spaces for the journey
     * @return boolean success
     */
    function FillSpace(){
        $returned = false;
        if(isset($this->journey)){
            $fill = $this->journey->Set("jr_spaces_available",($this->journey->Get("jr_spaces_available")-1));
            if($fill){
                $returned = $this->journey->Update();
            }            
        }
        return $returned;
    }
    
    /**
     * Loads journey to $this->journey
     * @param Integer $jr_pk
     * @return boolean success
     */
    function LoadJourney($jr_pk = ""){
        $returned = false;
        if($jr_pk != ""){
            $this->journey = new Journey($jr_pk);
            if(isset($this->journey)){
                $returned = true;
            }
        }
        return $returned;
    }
    
    /**
     * Returns all attributes for $this->journey object
     * @return Array
     */
    function GetJourneyDataAll(){
        $returned = false;
        if($this->journey){
            $returned = $this->journey->GetAll();
        }
        return $returned;
    }
    
    /**
     * Cancels a this journey in $this->journey
     * @return boolean success
     */
    function CancelJourney(){
        $returned = false;
        if(isset($this->journey)){
            $returned = $this->journey->Cancel();
        }
        return $returned;
    }
    
    /**
     * Searches for a journey using $this->jr_origin, $this->jr_destination,
     * $this->jr_search_date_1 and $this->search_date_2
     * @return array search results
     */
    function SearchJourney(){
        $returned = null;
        if($this->jr_origin && $this->jr_destination){
            $geolocation_result = $this->Geolocate();   
            if($geolocation_result && $this->search_date_1 && $this->search_date_2){
                $sql_string = $this->BuildSearchString();
                $response = pg_query($sql_string);
                if($response){
                    $search_array = pg_fetch_all($response);
                    if($search_array){
                        $this->search_results = $search_array;
                        $returned = $this->search_results;
                    }
                }
            }                      
        }
        return $returned;
    }  
    
    /**
     * Creates a new Journey object, sets its attributes to the values of the attributes in this class
     * and calls Create() in the journey object. Also uses google directions api to 
     * populate $this->google_directions_data
     * @return boolean success
     */
    function CreateJourney(){
        $returned = false;
        $this->journey = new Journey();
        $this->journey->Set("jr_ps_email", $this->jr_ps_email);
        $this->journey->Set("jr_origin", $this->jr_origin);
        $this->journey->Set("jr_destination", $this->jr_destination);
        $this->journey->Set("jr_etd", $this->jr_etd);
        $this->journey->Set("jr_eta", $this->jr_eta);
        $this->journey->Set("jr_total_spaces", $this->jr_total_spaces);
        $this->journey->Set("jr_spaces_available", $this->jr_spaces_available);
        
        if($this->BuildDirectionsUrl()){
            $directions_json = @file_get_contents($this->google_directions_url);
            $this->google_directions_data = json_decode($directions_json, true);
            
            $legs = $this->google_directions_data['routes'][0]['legs'];
            $jr_total_distance = 0;
            $jr_origin_latitude = $legs[0]['start_location']['lat'];
            $jr_origin_longitude = $legs[0]['start_location']['lng'];
            $jr_destination_latitude = $legs[count($legs) - 1]['end_location']['lat'];
            $jr_destination_longitude = $legs[count($legs) - 1]['end_location']['lng'];
            foreach($legs as $leg => $leg_value){
                $jr_total_distance = $jr_total_distance + $leg_value['distance']['value']/1000; 
            }
            $this->journey->Set("jr_origin_latitude", $jr_origin_latitude);
            $this->journey->Set("jr_origin_longitude", $jr_origin_longitude);
            $this->journey->Set("jr_destination_latitude", $jr_destination_latitude);
            $this->journey->Set("jr_destination_longitude", $jr_destination_longitude);
            $this->journey->Set("jr_total_distance", $jr_total_distance);        

            $journey_creation = $this->journey->Create();
            if($journey_creation){
                $journey_steps_creation = $this->CreateJourneySteps();
                if($journey_steps_creation){
                    $returned = true;                    
                }
            }
        }
        return $returned;
    }    
  
    /**
     * Creates journey steps for journey using the google directions data
     * @return boolean success
     */
    function CreateJourneySteps(){
        $returned = false;
        if(count($this->waypoints) == 0){
            $steps = $this->google_directions_data['routes'][0]['legs'][0]['steps'];            
        }
        else{
            $steps = Array();            
            $legs = $this->google_directions_data['routes'][0]['legs'];
            for($i=0; $i<count($legs); $i++){
                $leg_steps = $legs[$i]['steps'];
                for($j=0; $j<count($leg_steps); $j++){
                    array_push($steps, $leg_steps[$j]);
                }
            }
        }
        $create_steps = $this->journey_step_controller->CreateJourneySteps($this->journey->Get("jr_pk"), $steps);           
        if($create_steps){
            $returned = true;
        }
        return $returned; 
    }
    
    /**
     * Moves the old journey steps to journey_step_temp table, creates new journey steps using the 
     * google_directions_data, insert into database and deletes old journey steps in the 
     * journey_step_temp table
     * @return boolean success
     */
    function ModifyJourneySteps(){
        $returned = false;
        if($this->journey){            
            $url_build = $this->BuildDirectionsUrl();
            if($url_build){
                $direction_json = @file_get_contents($this->google_directions_url);
                $this->google_directions_data = json_decode($direction_json, true);
                $move_old = $this->MoveOldJourneySteps();
                if($move_old){
                    $create_new = $this->CreateJourneySteps();
                    if($create_new){
                        $delete_old = $this->DeleteOldJourneySteps($this->journey->Get("jr_pk"));
                        if($delete_old){
                            $spaces_left = $this->journey->Get("jr_spaces_available");
                            $this->journey->Set("jr_spaces_available", ($spaces_left - 1));
                            $returned = $this->journey->Update();
                        }
                    }
                }
            }
        }
        return $returned;
    }
    
    /**
     * moves old journey steps to journey_step_temp table
     * @return boolean success
     */
    private function MoveOldJourneySteps(){
        $returned = false;
        if(isset($this->journey)){
            $jr_pk = $this->journey->Get("jr_pk");
            $returned = $this->journey_step_controller->MoveTempJourneySteps($jr_pk);
        }
        return $returned;
    }
    
    /**
     * deletes old journey steps from journey_step_temp table
     * @param Integer $jr_pk
     * @return boolean success
     */
    private function DeleteOldJourneySteps($jr_pk){
        return $this->journey_step_controller->DeleteTempJourneySteps($jr_pk);
    }
    
    /**
     * Sets the waypoint array
     * @param array $array
     */
    function SetWaypointArray($array){
        $this->waypoint_array = Array();
        for($i=0; $i<count($array); $i++){
            array_push($this->waypoint_array, $array[$i]);
        }
    }
    
    /**
     * Returns a URL to use to retrieve directional data from google
     * @return string URL to retrieve directions data
     */
    function BuildDirectionsUrl(){
        $returned = false;
        $origin = $this->journey->Get("jr_origin");
        $destination = $this->journey->Get("jr_destination");
        if($origin != "" && $destination != ""){
            $url = "http://maps.googleapis.com/maps/api/directions/json?origin=".str_replace(' ','+', $origin, $x)."&destination=".str_replace(' ', '+', $destination, $y);
            if($this->waypoint_array){
                $url = $url."&waypoints=optimize:true|";
                for($i=0; $i<count($this->waypoint_array); $i++){
                    $url = $url.str_replace(" ", "+", $this->waypoint_array[$i], $x)."|";;
                }
                $url = rtrim($url, "|");
            }
            $url = $url."&amp;region=uk&sensor=false";
            $this->google_directions_url = $url;
            $returned = $url;
        }
        return $returned;
    }
    
    /**
     * uses $this->SetGeolocation() to set the locations of $this->jr_origin and 
     * $this->jr_destination. 
     * @return boolean success
     */
    function Geolocate(){
       $returned = false;
       $origin_result = $this->SetGeolocation("jr_origin", $this->jr_origin);
       $destination_result = $this->SetGeolocation("jr_destination", $this->jr_destination);  
       if($origin_result && $destination_result){
           $returned = true;
       }
       return $returned;
    }
    
    /**
     * Uses google geolocation API to set getlocation of palce name
     * @param String $origin_destination specify whether "jr_origin" or "jr_destination"
     * @param String $place_name
     * @return boolean success
     */
    private function SetGeolocation($origin_destination = "", $place_name = ""){
        $returned = false;
        if(($origin_destination != "") && ($place_name != "")){
            $place_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".str_replace(' ', '+', $place_name,$x)."&sensor=false";
            $place_json_data = @file_get_contents($place_url);
            $place_data = json_decode($place_json_data, true);
            $status = $place_data['status'];
            if($status != "OVER_QUERY_LIMIT"){  
                $origin_destination_lat = $origin_destination."_lat";
                $origin_destination_lng = $origin_destination."_lng";
                $geometry = $place_data['results'][0]['geometry']['location'];
                $this->$origin_destination_lat = $geometry['lat'];
                $this->$origin_destination_lng = $geometry['lng'];
                $returned = true;
            }
        }
        return $returned;
    }
    
    /**
     * Builds and SQL search string using the search dates, origin and destination names,
     * and the origin and destination location attributes
     * @return String SQL search string
     */
    private function BuildSearchString(){
        $sql_string = "select * from (select * from journey where jr_origin = '$this->jr_origin' and jr_destination = '$this->jr_destination' 
            and jr_etd >= '$this->search_date_1' and jr_etd <= '$this->search_date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                
            select * from journey where jr_origin_latitude - $this->jr_origin_lat < 0.04532
            and jr_origin_latitude - $this->jr_origin_lat > - 0.04532 and jr_origin_longitude - $this->jr_origin_lng
            < 0.041 and jr_origin_longitude - $this->jr_origin_lng > -0.041 and jr_destination_latitude
            - $this->jr_destination_lat < 0.04532 and jr_destination_latitude - $this->jr_destination_lat > -0.04532
            and jr_destination_longitude - $this->jr_destination_lng < 0.041 and jr_destination_longitude
            - $this->jr_destination_lng > -0.041 and jr_etd >= '$this->search_date_1' and jr_etd <= '$this->search_date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                
            select * from journey where jr_pk in (select distinct js_jr from journey_step where js_jr in (select jr_pk from journey where jr_origin_latitude - $this->jr_origin_lat <= 0.04532
            and jr_origin_latitude - $this->jr_origin_lat >= - 0.04532 and jr_origin_longitude - $this->jr_origin_lng
            <= 0.041 and jr_origin_longitude - $this->jr_origin_lng >= -0.041) and js_latitude - $this->jr_destination_lat >= -0.3153
            and js_latitude - $this->jr_destination_lat <= 0.3153 and js_longitude - $this->jr_destination_lng >= -0.35 and
            js_longitude - $this->jr_destination_lng <= 0.35) and jr_etd >= '$this->search_date_1' and jr_etd <= '$this->search_date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                
            select * from journey where jr_pk in (select distinct js_jr from journey_step where js_jr in (select jr_pk from journey where jr_destination_latitude - $this->jr_destination_lat <= 0.04532
            and jr_destination_latitude - $this->jr_destination_lat >= - 0.04532 and jr_destination_longitude - $this->jr_destination_lng
            <= 0.041 and jr_destination_longitude - $this->jr_destination_lng >= -0.041) and js_latitude - $this->jr_origin_lat >= -0.3153
            and js_latitude - $this->jr_origin_lat <= 0.3153 and js_longitude - $this->jr_origin_lng >= -0.35 and
            js_longitude - $this->jr_origin_lng <= 0.35) and jr_etd >= '$this->search_date_1' and jr_etd <= '$this->search_date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0 union
                
            select * from journey where jr_pk in (select distinct a.js_jr from journey_step a inner join journey_step b on a.js_jr = b.js_jr 
            where a.js_latitude - $this->jr_origin_lat >= -0.1 and a.js_latitude - $this->jr_origin_lat <= 0.1 and 
            a.js_longitude - $this->jr_origin_lng >= -0.09009 and a.js_longitude - $this->jr_origin_lng <= 0.09009 and 
            b.js_latitude - $this->jr_destination_lat >= -0.1 and b.js_latitude - $this->jr_destination_lat <= 0.1 and 
            b.js_longitude - $this->jr_destination_lng >= -0.09009 and b.js_longitude - $this->jr_destination_lng <= 0.09009)
            and jr_etd >= '$this->search_date_1' and jr_etd <= '$this->search_date_2' and jr_spaces_available > 0 and jr_is_cancelled = 0) journey order by jr_etd";
        //print_r($sql_string);
        return $sql_string;
        
    }
    
    
}

?>
