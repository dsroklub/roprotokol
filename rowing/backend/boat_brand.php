<?php
require("inc/common.php");
include("inc/utils.php");

$s="SELECT id,name FROM boat_brand ORDER by name";
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';
	  echo json_encode($row);
}
echo ']';
$rodb->close();
