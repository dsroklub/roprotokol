<?php
require("inc/common.php");
include("inc/utils.php");
$res=array ("status" => "ok");
$s="SELECT JSON_MERGE(
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
    ORDER by Name
    ";
//echo $s;
$result=$rodb->query($s) or dbErr($rodb,$res,"boattype query");
error_log(print_r($result,true));
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
}
echo ']';
$rodb->close();
