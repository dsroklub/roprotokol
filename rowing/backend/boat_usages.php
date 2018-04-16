<?php
require("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
$s="SELECT id,name, description FROM boat_usage ORDER by name";
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
