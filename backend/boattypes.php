<?php
require("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Name as name ,Seatcount as seatcount, Category, GROUP_CONCAT(required_right,':§§:',requirement SEPARATOR '££') AS rights
    FROM BoatType
    LEFT JOIN  BoatRights ON BoatType.Name=boat_type
    GROUP BY BoatType.Name
    ORDER by Name
    ";
//echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
      $row['rights']=multifield($row['rights']);
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
