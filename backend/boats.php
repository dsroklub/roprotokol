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
           BoatType.Name as category,
           BoatCategory.Name as boattype,
           Location.Name as location,
           Boat.Location as location_id,
           Boat.Placement as placement
    FROM Boat
         INNER JOIN BoatType ON (BoatType.id=BoatType)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
         LEFT OUTER JOIN Location ON (Location.id = Boat.Location)
    WHERE 
         Boat.Decommissioned IS NULL
    ";




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
