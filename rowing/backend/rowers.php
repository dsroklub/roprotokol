<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

// TODO when we can use Mysql 8 replace with JSON_ARRAYAGG etc
$s="SELECT JSON_MERGE(
    JSON_OBJECT(
      'id',Member.MemberID, 
      'name', CONCAT(FirstName,' ',LastName) 
   ),
   CONCAT(
  '{', JSON_QUOTE('rights'),': [',
     GROUP_CONCAT(JSON_OBJECT(
      'member_right',MemberRight,'arg',argument,'acquired',Acquired)),
   ']}')
   ) AS json
   FROM Member LEFT JOIN MemberRights on MemberRights.member_id=Member.id  
   WHERE Member.MemberID!='0' AND Member.id>=0
   GROUP BY Member.id";

if ($sqldebug) {
    echo $s."<br>\n";
}
$result=$rodb->query($s);

if (!$result) {
    http_response_code(500);
    die("Error in rowers query: " . mysqli_error($rodb));;
}

echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
}
echo ']';
$rodb->close();
