<?php
require("inc/common.php");
include("inc/utils.php");

$s="SELECT Boat.id as boat_id,Boat.Name as boat, start_time,start_date,end_time,end_date,dayofweek,reservation.description,TripType.Name as triptype, TripType.id as triptype_id,purpose 
    FROM reservation,Boat,TripType 
    WHERE Boat.id=reservation.boat AND TripType.id=reservation.triptype
          AND (start_date>NOW() OR dayofweek>0)
    ORDER BY boat,start_date,dayofweek,start_time
";
$result=$rodb->query($s) or die("Error in event query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
