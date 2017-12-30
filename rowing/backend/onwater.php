<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT JSON_MERGE(
    JSON_OBJECT(
    'boatid', Boat.id, 
    'boat',Boat.Name,
    'outtime',DATE_FORMAT(OutTime,'%Y-%m-%dT%T'), 
    'expectedintime',DATE_FORMAT(ExpectedIn,'%Y-%m-%dT%T'), 
    'destination',Trip.Destination, 
    'id',Trip.id, 
    'triptype',TripType.Name
    ),
    CONCAT('{\"rowers\" : [',
    GROUP_CONCAT(
     JSON_OBJECT(
      'member_id',Member.MemberID, 
      'name',CONCAT(Member.FirstName,' ',Member.LastName)) ORDER BY Seat),
      ']}')
    ) as json
   FROM TripMember LEFT JOIN Member ON Member.id = TripMember.member_id, TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID 
   WHERE Trip.id=TripMember.TripID AND Trip.InTime Is Null GROUP BY Trip.id ORDER BY ExpectedIn";

if ($sqldebug) {
  echo $s;
  echo "\n";
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
