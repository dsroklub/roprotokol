<?php
require("inc/common.php");
include("inc/utils.php");

$s="SELECT id,Name as name ,Seatcount as seatcount, Category as category, Description as description,rights_subtype,
           GROUP_CONCAT(required_right,':§§:',requirement SEPARATOR '££') AS rights
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
	  if ($first) $first=0; else echo ',';	  
      $row['rights']=multifield($row['rights']);
	  echo json_encode($row,JSON_FORCE_OBJECT);
}
echo ']';
$rodb->close();
?> 
