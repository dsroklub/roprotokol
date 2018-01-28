<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
header('Content-type: application/json');

$s="SELECT JSON_MERGE(
    JSON_OBJECT(
      'id',Member.MemberID, 
       'phone',member_setting.phone,
      'name', CONCAT(FirstName,' ',LastName) 
   ),
   CONCAT('{\"rights\" : [',
     GROUP_CONCAT(JSON_OBJECT('member_right',MemberRight,'arg',argument,'acquired',Acquired)),
   ']}')
   ) AS json
   FROM member_setting,Member LEFT JOIN MemberRights on MemberRights.member_id=Member.id  
   WHERE Member.MemberID!='0' AND
   member_setting.member=Member.id
   GROUP BY Member.id";

if ($sqldebug) {
    echo $s."<br>\n";
}
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo $row['json'];
}
echo ']';
$rodb->close();
?> 
