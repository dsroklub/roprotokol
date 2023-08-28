<?php
require("inc/common.php");
include("inc/utils.php");
$bt=$rodb->query("SELECT JSON_OBJECT(
     'name',Name,
     'seatcount',Seatcount ,
     'category',Category,
     'rights_subtype',rights_subtype,
     'description',Description,
     'rights', JSON_ARRAYAGG(JSON_OBJECT('required_right',required_right,'requirement',requirement))
    ) as json
    FROM BoatType
    LEFT JOIN  BoatRights ON BoatType.Name=BoatRights.boat_type
    GROUP BY BoatType.Name,Seatcount,Category,rights_subtype,Description
    ORDER by Name") or dbErr($rodb,$res,"boattypes");
output_json($bt);
$rodb->close();
