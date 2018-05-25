<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT Damage.id,Damage.Boat as boat_id,BoatType.name as boattype,Boat.Name as boat,Damage.Description as description,Degree as degree,Damage.ResponsibleMember,RepairerMember,Repaired as repaired, Damage.Created AS created, CONCAT(FirstName,' ',LastName) as reporter ".
    " FROM Boat, BoatType, Damage
      LEFT OUTER JOIN Member ON Member.id=Damage.ResponsibleMember
      WHERE Damage.Boat=Boat.id AND Boat.BoatType=BoatType.id AND Repaired IS NULL ORDER BY Boat,degree DESC
 ";

# echo $s;
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
     echo '[';
     $first=1;
     while ($row = $result->fetch_assoc()) {
       if ($first) $first=0; else echo ',';	  
       echo json_encode($row);
     }
     echo ']';
}
$rodb->close();
?> 
