<?php
include("../../rowing/backend/inc/common.php");

$s="SELECT forum.name,forum.description, Member.MemberId as owner,is_open
    FROM forum,Member 
    WHERE Member.id=forum.owner";

$result=$rodb->query($s) or die("Error in fora query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',
';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
