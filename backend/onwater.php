<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Boat.Name AS Boat, OutTime, ExpectedIn, Trip.Destination, Trip.TripID, TripType.Name AS Triptype ".
  " FROM TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID ".
  " WHERE ((Trip.InTime Is Null)) ORDER BY ExpectedIn";

// echo $s;
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
