<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT id, Name as name, Meter as distance, ExpectedDurationNormal AS duration,  ExpectedDurationInstruction AS duration_instruction
    FROM Destination ORDER BY name";


// echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
