<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s=<<<SQT
SELECT id,Active as active, Name AS name,Description AS description, GROUP_CONCAT(required_right,':§§:',requirement SEPARATOR '££') AS rights 
     FROM TripType 
     LEFT JOIN TripRights ON TripRights.trip_type=TripType.id GROUP BY TripType.id,TripType.Name ORDER BY name;
SQT
;
// $s="SELECT TurTypeID as id, Navn as name FROM TurType ORDER BY id";


$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';
      $row['rights']=multifield($row['rights']);
	  echo json_encode($row,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);
}
echo ']';
$rodb->close();
?>
