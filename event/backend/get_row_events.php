<?php
require("inc/common.php");
include("inc/utils.php");
//verify_right(["admin" => [null],"rodata"=>[null]]);

$s="SELECT event, event_time FROM event_log ORDER BY event_time DESC LIMIT 500";
$result=$rodb->query($s) or die("Error in event query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';
	  echo json_encode($row);
}
echo ']';
$rodb->close();
