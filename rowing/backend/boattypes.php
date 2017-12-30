<?php
require("inc/common.php");
include("inc/utils.php");

$s="SELECT JSON_MERGE(
    JSON_OBJECT(
     'id',id,
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
    LEFT JOIN  BoatRights ON BoatType.id=boat_type
    GROUP BY BoatType.Name,BoatType.id
    ORDER by Name
    ";
//echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
}
echo ']';
$rodb->close();
