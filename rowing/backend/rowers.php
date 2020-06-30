<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

// TODO when we can use Mariadb 10.5 replace with JSON_ARRAYAGG etc
$s="SELECT JSON_MERGE(
    JSON_OBJECT(
      'id',Member.MemberID,
      'club',Member.club,
      'status', IF(RemoveDate,'ikke medlem',IF(member_type=1,'passiv','ok')),
      'name', CONCAT(FirstName,' ',LastName)
   ),
   CONCAT(
  '{', JSON_QUOTE('rights'),': [',
     GROUP_CONCAT(JSON_OBJECT(
      'member_right',MemberRight,'arg',argument,'acquired',Acquired)),
   ']}')
   ) AS json
   FROM Member LEFT JOIN MemberRights ON MemberRights.member_id=Member.id
   WHERE Member.MemberID!='0' AND Member.id>=0 AND
     (member_type <> -1 OR member_type IS NULL)
   GROUP BY Member.id";

if ($sqldebug) {
    echo $s."<br>\n";
}
$result=$rodb->query($s) or dbErr($rodb,$res,"get rowers");
output_json($result);
$rodb->close();
