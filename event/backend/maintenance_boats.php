<?php
require("../../rowing/backend/inc/common.php");
header('Content-type: application/json');

$s="SELECT JSON_OBJECT(
           'id', Boat.id,
           'name', Boat.Name,
           'spaces',BoatType.Seatcount,
           'description', Boat.Description,
           'category',BoatType.Name,
           'boat_type', Boat.boat_type,
           'location', Boat.Location,
           'brand',Boat.brand,
           'level',Boat.level) as json
    FROM Damage,Boat
         INNER JOIN BoatType ON (BoatType.Name=Boat.boat_type)
         INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
    WHERE
         Boat.Decommissioned IS NULL AND
         Damage.boat=Boat.id AND
         Damage.Repaired IS NULL AND
         Damage.Degree=$damageMaintenance
    ORDER by Boat.name
    ";
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in boats query");
output_json($result);
$rodb->close();
