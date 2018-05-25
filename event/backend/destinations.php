<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
header('Content-type: application/json');

$s="SELECT Destination.Location as location, Destination.Name as name, Meter as distance, ExpectedDurationNormal AS duration, ExpectedDurationInstruction AS duration_instruction
   FROM Destination
   ORDER BY Location,name";

if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in destinations query: " . mysqli_error($rodb));
     $first=1;
     $d=[];
     while ($row = $result->fetch_assoc()) {
       $loc=$row['location'];
       if (!isset($d[$loc])) {
         $d[$loc]=array();
       }
       array_push($d[$loc],$row);
     }
     echo json_encode($d);
}
$rodb->close();
?> 
