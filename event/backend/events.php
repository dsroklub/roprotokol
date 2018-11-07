<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$res=array ("status" => "ok");

$s="SELECT Member.MemberId AS owner, CONCAT(Member.FirstName,' ',Member.LastName) as owner_name, event.name, 
    BoatCategory.Name as boat_category, start_time,end_time, 
    distance, destination, TripType.Name as trip_type, max_participants, location, category, preferred_intensity, comment, event.open,event.status
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
    $res["status"]=$rodb->error;
    echo json_encode($res);
}
