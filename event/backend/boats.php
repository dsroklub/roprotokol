<?php
// Copy from roprotokol

require("../../rowing/backend/inc/common.php");
header('Content-type: application/json');

$s="SELECT DISTINCT Boat.Name,JSON_OBJECT(
           'id', Boat.id,
           'name', Boat.Name,
           'spaces',BoatType.Seatcount,
           'description', Boat.Description,
           'category',BoatType.Name,
           'boat_type', Boat.boat_type,
           'max_hours', baad.max_timer,
           'location', Boat.Location,
           'brand',Boat.brand,
           'forum',MAX(forum.name),
           'formand',MIN(baadformand.formand),
           'level',Boat.level) as json
    FROM Boat
         INNER JOIN BoatType ON (BoatType.Name=Boat.boat_type)
           INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
           LEFT JOIN (dsrvinter.baad JOIN dsrvinter.baadformand ON baadformand.baad=baad.id) ON baad.navn=Boat.name
           LEFT JOIN forum ON forum.boat=Boat.name AND forum.forumtype='maintenance'
    WHERE
         Boat.Decommissioned IS NULL
    GROUP BY Boat.id,baad.max_timer
    ORDER by Boat.Name
    ";
//echo $s;
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in boats query");
output_json($result);
$rodb->close();
