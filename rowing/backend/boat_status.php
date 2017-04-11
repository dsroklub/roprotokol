<?php
include("inc/common.php");
header('Content-type: application/json');

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

$s="SELECT Boat.id,
           Boat.Name as name,
           BoatType.Seatcount as spaces,
           Boat.Description as description,
           BoatCategory.Name as boattype,
           BoatType.Name as category,
           Boat.Location as location,
           Boat.placement_aisle,
           Boat.placement_level,
           Boat.placement_row,
           Boat.placement_side,
           Boat.boat_usage as usage_id,
           COALESCE(MAX(Damage.Degree),0) as damage,
           MAX(Trip.id) as trip,
           MAX(Trip.OutTime) as outtime,
           MAX(Trip.ExpectedIn) as expected_in,
           MAX(Trip.Destination) as destination,
           MAX(Trip.Meter) as meter,
           MAX(Trip.Comment) as comment,
           boat_usage.name as boatusage,
           Boat.brand,
           Boat.level
    FROM Boat
         INNER JOIN BoatType ON (BoatType.id=BoatType)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
         LEFT OUTER JOIN Damage ON (Damage.Boat=Boat.id AND Damage.Repaired IS NULL)
         LEFT OUTER JOIN Trip ON (Trip.BoatID = Boat.id AND Trip.Intime IS NULL)
         LEFT JOIN boat_usage ON Boat.boat_usage=boat_usage.id
    WHERE 
         Boat.Decommissioned IS NULL
    GROUP BY
       Boat.id,
       Boat.Name
    ORDER BY Boat.Name
    ";


if ($sqldebug) {
 echo $s;
}
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
