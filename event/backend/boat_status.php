<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT Boat.id,
           Boat.Name as name,
           BoatType.Seatcount as spaces,
           Boat.Description as description,
           BoatCategory.Name as boat_type,
           Boat.boat_type as category,
           Boat.Location as location,
           Boat.placement_aisle,
           Boat.placement_level,
           Boat.placement_row,
           Boat.placement_side,
           COALESCE(MAX(Damage.Degree),0) as damage,
           MAX(Trip.id) as trip,
           MAX(Trip.OutTime) as outtime,
           MAX(Trip.ExpectedIn) as expected_in,
           MAX(Trip.Destination) as destination,
           MAX(Trip.Meter) as meter,
           MAX(Trip.Comment) as comment,
           Boat.boat_use as 'usage',
           Boat.brand,
           Boat.note,
           Boat.oar_pitch,
           Boat.oar_height,
           Boat.oar_type,
           Boat.rig_height,
           Boat.level
    FROM Boat
         INNER JOIN BoatType ON (BoatType.Name=Boat.boat_type)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
         LEFT OUTER JOIN Damage ON (Damage.Boat=Boat.id AND Damage.Repaired IS NULL)
         LEFT OUTER JOIN Trip ON (Trip.BoatID = Boat.id AND Trip.Intime IS NULL)
    GROUP BY
       Boat.id,
       Boat.Name,
       BoatCategory.Name,
       BoatType.Seatcount
    ORDER BY Boat.Name
    ";


if ($sqldebug) {
 echo $s;
}
$result=$rodb->query($s) or dbErr($rodb,$res,"Q");
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo json_encode($row);
}
echo ']';
$rodb->close();
