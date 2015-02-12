<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Destination.Location as location, Destination.Name as name, Meter as distance,ExpectedDurationNormal duration, ExpectedDurationInstruction  AS duration_instruction
   FROM Destination
   ORDER BY Location,name";

// echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
$d="";
 while ($row = $result->fetch_assoc()) {
   $loc=$row['location'];
   if (!isset($d[$loc])) {
     $d[$loc]=array();
   }
   array_push($d[$loc],$row);
}
echo json_encode($d);
echo ']';
$rodb->close();
?> 
