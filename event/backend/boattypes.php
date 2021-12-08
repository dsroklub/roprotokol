<?php
include("inc/common.php");
$bt=$rodb->query("SELECT JSON_MERGE(
    JSON_OBJECT(
     'name',Name,
     'seatcount',Seatcount ,
     'category',Category,
     'rights_subtype',rights_subtype,
     'description',Description
    ),
      CONCAT('{\"rights\" : [',
           GROUP_CONCAT(JSON_OBJECT('required_right',required_right,'requirement',requirement)) ,
      ']}')
    ) as json
    FROM BoatType
    LEFT JOIN  BoatRights ON BoatType.Name=BoatRights.boat_type
    GROUP BY BoatType.Name,Seatcount,Category,rights_subtype,Description
    ORDER by Name") or dbErr($rodb,$res,"boattypes");
//echo $s;
output_json($bt);
$rodb->close();
