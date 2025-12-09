<?php
require("inc/common.php");
header('Content-type: application/json');
$cuser=$_SERVER['PHP_AUTH_USER'];
$locationClause="";
if (empty($cuser) || $cuser!='bagsvaerd') {
    $locationClause=" AND Boat.location!=\"Bagsværd\" ";
} else {
    $locationClause=" AND Boat.location!=\"DSR\" ";
}
    

$s="SELECT JSON_OBJECT(
           'id', Boat.id,
           'name', Boat.Name,
           'spaces',BoatType.Seatcount,
           'description', Boat.Description,
           'category',BoatType.Name,
           'boat_type', Boat.boat_type,
           'location', Boat.Location,
           'brand',Boat.brand,
           'note',Boat.note,
           'usage',Boat.boat_use,
           'level',Boat.level) as json
    FROM Boat
         INNER JOIN BoatType ON (BoatType.Name=Boat.boat_type)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
    WHERE
         Boat.Decommissioned IS NULL $locationClause
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
