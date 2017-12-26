<?php
require("inc/common.php");
include("inc/utils.php");

$s="SELECT name,description
    FROM instruction_team
    ";
$result=$rodb->query($s) or die("Error in cox team query: " . mysqli_error($rodb));
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row,JSON_FORCE_OBJECT);
}
echo ']';
$rodb->close();
