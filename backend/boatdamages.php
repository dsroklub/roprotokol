<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT Damage.id,Damage.Boat as boat_id,Boat.Name as boat,Damage.Description as description,Degree as level,ResponsibleMember,RepairerMember,Repaired as repaired, Damage.Created as created ".
    " FROM Damage,Boat WHERE Damage.Boat=Boat.id AND Repaired IS NULL ORDER BY Boat, level";

//echo $s;
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
