<?php
require("inc/common.php");
header('Content-type: application/json');

$s="SELECT Boat.id,
           Boat.Name as name,
           BoatType.Seatcount as spaces,
           Boat.Description as description,
           BoatType.Name as category,
           BoatCategory.Name as boattype,
           Boat.Location as location,
           Boat.brand,
           Boat.level
    FROM Boat
         INNER JOIN BoatType ON (BoatType.id=BoatType)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)         
    WHERE 
         Boat.Decommissioned IS NULL
    GROUP BY id
    ";
//echo $s;
$result=$rodb->query($s) or die("Error in boats query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
