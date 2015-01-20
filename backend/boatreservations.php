<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT id, Boat as boat_id, Begin as start, End as end, Description as description FROM Reservation WHERE End > Now()";
// for debug    $s="SELECT ID as id, FK_BådID as boat_id, start, slut as end, Beskrivelse as description FROM Reservation WHERE slut > '2013-08-30'";


// echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
