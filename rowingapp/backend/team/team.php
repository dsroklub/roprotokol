<?php

set_include_path(get_include_path().':..');
include("inc/common.php");
$s='SELECT team.name,description,dayofweek,teacher,timeofday, weekday(NOW())+1=no AS today 
    FROM team,weekday
    WHERE weekday.name=team.dayofweek
    ORDER BY no,timeofday,team.name,teacher
';
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;

echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';

?> 
