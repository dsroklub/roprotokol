<?php
include("inc/common.php");
$damages=$rodb->query(
    "SELECT Damage.id,DamageType.name as damage_name,Damage.Boat AS boat_id,Boat.boat_type,Boat.Name AS boat,Damage.Description as description,Damage.Degree AS degree,Damage.ResponsibleMember,RepairerMember,Repaired AS repaired, Damage.Created AS created, CONCAT(FirstName,' ',LastName) AS reporter
     FROM DamageType,Boat,Damage
      LEFT OUTER JOIN Member ON Member.id=Damage.ResponsibleMember
      WHERE Boat.Decommissioned IS NULL AND Damage.Boat=Boat.id AND DamageType.degree=Damage.degree AND Repaired IS NULL ORDER BY Boat,degree DESC") or dbErr($rodb,$res,"get boatdamages");
# echo $s;
process($damages);
$rodb->close();
