<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s=<<<SQT
SELECT JSON_MERGE(
     JSON_OBJECT(
       'id',id,
       'tripstat_name',tripstat_name,
        'active',Active,
        'name',Name,
        'description',Description
        ),
           CONCAT('{\"rights\" : [',
                      GROUP_CONCAT(JSON_OBJECT('required_right',required_right,'requirement',requirement)) ,
      ']}')
      ) as json
     FROM TripType
     LEFT JOIN TripRights ON TripRights.trip_type=TripType.id GROUP BY TripType.id,TripType.Name ORDER BY name;
SQT
;

if ($sqldebug) echo "$s\n";

$result=$rodb->query($s) or dbErr($rodb,$res,"GET triptypes");
output_json($result);
$rodb->close();
