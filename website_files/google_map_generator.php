<?php


function GetMap($journey_array, $waypoints){
    $jr_origin = $journey_array["jr_origin"];
    $jr_destination = $journey_array["jr_destination"];
    
    if($waypoints){
        $waypoint_string = "waypoints:[";
        for($i=0; $i<count($waypoints); $i++){
            $waypoint_string = $waypoint_string."{location: '$waypoints[$i]'},";
        }
        $waypoint_string = rtrim($waypoint_string, ",");
        $waypoint_string = $waypoint_string."],";
    }
    
    $javascript = "<script>

        var rendererOptions = {
            draggable: false
        };

        var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
        var directionsService = new google.maps.DirectionsService();
        var map;

        function initialize() {
            var mapOptions = {
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
            directionsDisplay.setMap(map);
            calcRoute();
        }

        function calcRoute() {
            var request = {
                origin: '$jr_origin',
                destination: '$jr_destination',
                $waypoint_string
                optimizeWaypoints: true,
                travelMode: google.maps.DirectionsTravelMode.DRIVING,
                                    provideRouteAlternatives: false
            };
            directionsService.route(request, function(response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);

    </script>";
    return $javascript;
}

?>
