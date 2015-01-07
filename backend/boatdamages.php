<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT SkadeID as id, FK_BådID as boat_id, Beskrivelse as description, Grad as level
    FROM Skade WHERE Repareret IS NULL ORDER BY  FK_BådID, level";

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
