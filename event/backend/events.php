<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$s="SELECT Member.MemberId AS owner, event.name, BoatCategory.Name as boat_category, start_time,end_time, distance, TripType.Name as trip_type, max_participants, location, category, preferred_intensity, comment, event.open
FROM Member, (event LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category) LEFT JOIN TripType ON TripType.id=event.trip_type 
WHERE Member.id=event.owner AND start_time >= NOW()";

$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';	  
        echo json_encode($row);
    }
    echo ']';
} else {
    http_response_code(500);
    echo json_encode($res);
}

?> 
