<?php

require '../api/hitch_request_controller.php';

$hr_pk = 8;
$hr_controller = new Hitch_Request_Controller($hr_pk);
print_r($hr_controller->AcceptHitchRequest2());    

?>
