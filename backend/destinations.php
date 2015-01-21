<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Destination.id,
           Destination.Name as name,
           GROUP_CONCAT(Location.Name,':§§:',Meter  SEPARATOR '££') as distance,
           GROUP_CONCAT(Location.Name,':§§:',ExpectedDurationNormal SEPARATOR '££') AS duration, 
           GROUP_CONCAT(Location.Name,':§§:',ExpectedDurationInstruction SEPARATOR '££')  AS duration_instruction
   FROM Destination
        LEFT OUTER JOIN Location ON (Location.id = Destination.Location) 
   GROUP BY Destination.Name
   ORDER BY name" ;

// echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
    $row['distance']=multifield($row['distance']);
    $row['duration']=multifield($row['duration']);
    $row['duration_instruction']=multifield($row['duration_instruction']);
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
