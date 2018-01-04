<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s=<<<SQT
SELECT JSON_MERGE(
     JSON_OBJECT(
       'id',id,
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
// $s="SELECT TurTypeID as id, Navn as name FROM TurType ORDER BY id";

if ($sqldebug) echo "$s\n";

$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
}
echo ']';
$rodb->close();
