<?php
// Copy from roprotokol

require("../../rowing/backend/inc/common.php");
header('Content-type: application/json');

$s="SELECT JSON_OBJECT(
           'id', Boat.id,
           'name', Boat.Name,
           'spaces',BoatType.Seatcount,
           'description', Boat.Description,
           'category',BoatType.Name,
           'boat_type', Boat.boat_type,
           'location', Boat.Location,
           'brand',Boat.brand,
           'level',Boat.level) as json
    FROM Boat
         INNER JOIN BoatType ON (BoatType.Name=Boat.boat_type)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)         
    WHERE 
         Boat.Decommissioned IS NULL
    GROUP BY Boat.id
    ORDER by Boat.name
    ";
//echo $s;
$result=$rodb->query($s) or die("Error in boats query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";	  
	  echo $row['json'];
}
echo ']';
$rodb->close();
