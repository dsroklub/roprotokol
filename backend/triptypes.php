<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s=<<<SQT
select Navn AS name, Beskrivelse as description, GROUP_CONCAT(required_right,':§§:',requirement SEPARATOR '££') as rights from TurType, TripRights WHERE aktiv AND trip_type=Navn GROUP BY TurType.Navn;
SQT
;
// $s="SELECT TurTypeID as id, Navn as name FROM TurType ORDER BY id";


$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';
      $row['rights']=multifield($row['rights']);
	  echo json_encode($row,JSON_PRETTY_PRINT);
}
echo ']';
$rodb->close();
?>
