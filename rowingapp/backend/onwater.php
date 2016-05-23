<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Boat.id as boatid, Boat.Name AS boat, OutTime as outtime, ExpectedIn as expectedintime, Trip.Destination as destination, Trip.id, TripType.Name AS triptype,GROUP_CONCAT(Member.MemberID,':§§:', 
   CONCAT(Member.FirstName,' ',Member.LastName) ORDER BY Seat SEPARATOR '££' ) AS rowers 
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
	  if ($first) $first=0; else echo ',';
      $row['rowers']=multifield_array($row['rowers'],"member_id","name");
//      print_r($row);
	  echo json_encode($row,JSON_PRETTY_PRINT);
}
echo ']';
$rodb->close();
?> 
