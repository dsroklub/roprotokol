<?php
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT Damage.id,Damage.Boat as boat_id,Boat.Name as boat,Damage.Description as description,Degree as degree,Damage.ResponsibleMember,RepairerMember,Repaired as repaired, Damage.Created AS CREATED, CONCAT(FirstName,' ',LastName) as reporter ".
    " FROM Boat, Damage
      LEFT OUTER JOIN Member ON Member.MemberID=Damage.ResponsibleMember
      WHERE Damage.Boat=Boat.id AND Repaired IS NULL ORDER BY Boat,degree
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
