<?php

require_once 'api.php';
require_once 'journey_step.php';

/**
 * controller of the journey_step model class
 * @author Alex Roan <alr16@aber.ac.uk>
 */
class Journey_Step_Controller extends API{
    
    var $journey_steps = null;
    var $old_journey_steps = null;
    
    /**
     * Constructor
     */
    function __construct(){
        $this->ConnectToDatabase();
    }
    
    /**
     * Constructor
     */
    function Journey_step_controller(){
        $this->__construct();
    }    
    
    /**
     * Moves old journey steps to journey_step_temp table
     * @param Integer $jr_pk
     * @return boolean success
     */
    function MoveTempJourneySteps($jr_pk = ""){
        $returned = false;
        if($jr_pk != ""){
            $sql_string = "select * from journey_step where js_jr = $jr_pk";
            $response = pg_query($sql_string);
            if($response){
                $this->old_journey_steps = Array();
                $steps_array = pg_fetch_all($response);
                $number_of_rows = pg_num_rows($response);
                for($i = 0; $i < $number_of_rows; $i++){
                    $row = $steps_array[$i];
                    $journey_step = new Journey_Step($row['js_pk']);
                    $temp_response = $journey_step->CreateTemp();
                    if($temp_response){                        
                        array_push($this->old_journey_steps, $journey_step); 
                        $journey_step->Delete();
                    }                    
                }
                if($number_of_rows == count($this->old_journey_steps)){
                    $returned = true;
                }                               
            }
        }
        return $returned;
    }
    
    /**
     * Deletes old journey steps from journey_step_temp table
     * @param Integer $jr_pk
     * @return boolean success
     */
    function DeleteTempJourneySteps($jr_pk){
        $returned = false;
        $sql_string = "delete from journey_step_temp where st_jr = $jr_pk";
        $response = pg_query($sql_string);
        if($response){
            $returned = true;
        }
        return $returned;
    }
    
    /**
     * Creates new journey_steps and pushes the objects to $this->journey_steps
     * @param Integer $jr_pk
     * @param Array $steps
     * @return boolean success
     */
    function CreateJourneySteps($jr_pk, $steps){
        $returned = false;
        $this->journey_steps = Array();
        for($i=0; $i<count($steps); $i++){
            $journey_step = new Journey_Step();
            $journey_step->Set("js_jr", $jr_pk);
            $journey_step->Set("js_step_order", $i);
            $journey_step->Set("js_latitude", $steps[$i]['end_location']['lat']);
            $journey_step->Set("js_longitude", $steps[$i]['end_location']['lng']);
            array_push($this->journey_steps, $journey_step);
            $returned = $journey_step->Create();
        }        
        return $returned;
    }
}

?>
