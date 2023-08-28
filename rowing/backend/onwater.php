<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s=" SELECT JSON_MERGE(
    ow.json ,
    JSON_OBJECT('damage',COALESCE(MAX(Damage.Degree),0))) as json FROM (SELECT
    Boat.id  boat_id,
    Trip.id trip_id,
    JSON_OBJECT(
       'boat_id', Boat.id,
       'boat',Boat.Name,
        'outtime',DATE_FORMAT(OutTime,'%Y-%m-%dT%T'),
        'expectedintime',DATE_FORMAT(ExpectedIn,'%Y-%m-%dT%T'),
        'destination',Trip.Destination,
        'trip_id',Trip.id,
        'comment',Trip.Comment,
        'distance',Trip.Meter,
        'triptype',TripType.Name,
        'triptype_id',TripType.id,
        'boat_type', Boat.boat_type,
        'boat_placement_aisle',  Boat.placement_aisle,
        'boat_placement_level',  Boat.placement_level,
        'boat_placement_row',    Boat.placement_row,
        'boat_placement_side',   Boat.placement_side,
        'rowers',JSON_ARRAYAGG(JSON_OBJECT(
          'member_id',Member.MemberID,
          'name',CONCAT(Member.FirstName,' ',Member.LastName)) ORDER BY Seat)
    ) as json
   FROM TripMember LEFT JOIN Member ON Member.id = TripMember.member_id, TripType RIGHT JOIN Boat  RIGHT JOIN Trip ON Boat.id = Trip.BoatID ON TripType.id = Trip.TripTypeID
   WHERE Trip.id=TripMember.TripID AND Trip.InTime Is Null
   GROUP BY Trip.id,Boat.id
   ORDER BY ExpectedIn) AS ow LEFT OUTER JOIN Damage ON (Damage.Boat=ow.boat_id AND Damage.Repaired IS NULL) GROUP BY ow.trip_id";

if ($sqldebug) {
  echo $s . "\n";
}
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
}
echo ']';
$rodb->close();
