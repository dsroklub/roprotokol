<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Boat.id as boatid, Boat.Name AS boat, OutTime as outtime, ExpectedIn as exptectedintime, Trip.Destination as destination, Trip.TripID as id, TripType.Name AS triptype ".
  " FROM TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID ".
  " WHERE ((Trip.InTime Is Null)) ORDER BY ExpectedIn";

#echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';
	  echo json_encode($row,JSON_PRETTY_PRINT);
}
echo ']';
$rodb->close();
?> 
