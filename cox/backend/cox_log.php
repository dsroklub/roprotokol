<?php
include("../../rowing/backend/inc/common.php");

$s='SELECT Member.MemberId as instructor, CONCAT(Member.FirstName," ", Member.LastName) as instructor_name,timestamp,entry,action
    FROM cox_log, Member WHERE Member.MemberId=cox_log.member_id
    GROUP BY timestamp DESC
';


$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
?> 


