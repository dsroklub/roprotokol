<?php
require("inc/common.php");
$s="SELECT DISTINCT Boat.Name,JSON_OBJECT(
           'id', Boat.id,
           'name', Boat.Name,
           'spaces',BoatType.Seatcount,
           'description', Boat.Description,
           'category',BoatType.Name,
           'boat_type', Boat.boat_type,
           'location', Boat.Location,
           'brand',Boat.brand,
           'forum',MAX(forum.name),
           'level',Boat.level) as json
    FROM Boat
         INNER JOIN BoatType ON (BoatType.Name=Boat.boat_type)
           INNER JOIN BoatCategory ON (BoatCategory.id = BoatType.Category)
           LEFT JOIN forum ON forum.boat=Boat.name AND forum.forumtype='maintenance'
    WHERE
         Boat.Decommissioned IS NULL
    GROUP BY Boat.id
    ORDER by Boat.Name
    ";
//echo $s;
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in boats query");
output_json($result);
$rodb->close();
