<?php
include("inc/common.php");
header('Content-type: application/json');

$damages=$rodb->query(
    "SELECT Damage.id,Damage.Boat AS boat_id,Boat.boat_type AS boat_type,Boat.Name AS boat,Damage.Description as description,Degree AS degree,Damage.ResponsibleMember,RepairerMember,Repaired AS repaired, Damage.Created AS created, CONCAT(FirstName,' ',LastName) AS reporter
     FROM Boat,Damage
      LEFT OUTER JOIN Member ON Member.id=Damage.ResponsibleMember
      WHERE Damage.Boat=Boat.id AND Repaired IS NULL ORDER BY Boat,degree DESC
 ") or dbErr($rodb,$res,"get boatdamages");
# echo $s;
process($damages);
$rodb->close();
