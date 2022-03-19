<?php
include("inc/common.php");
include("inc/utils.php");
$currentClause="";
if (!verify_right(["admin"=>["roprotokol"]],false)) {
  $currentClause=" AND RemoveDate IS NULL";
}
$s="SELECT JSON_MERGE(
    JSON_OBJECT(
      'id',Member.MemberID,
      'phone',member_setting.phone,
      'email_shared',member_setting.email_shared,
      'status', IF(RemoveDate,'ikke medlem',IF(member_type=1,'passiv','ok')),
      'name', CONCAT(FirstName,' ',LastName)
   ),
   CONCAT('{\"rights\" : [',
     GROUP_CONCAT(JSON_OBJECT('member_right',MemberRight,'arg',argument,'acquired',Acquired)),
   ']}')
   ) AS json
   FROM Member
     LEFT JOIN member_setting ON member_setting.member=Member.id
     LEFT JOIN MemberRights on MemberRights.member_id=Member.id
   WHERE Member.MemberID!='0' $currentClause
   GROUP BY Member.id";

if ($sqldebug) {
    echo $s."<br>\n";
}
$result=$rodb->query($s) or dbErr($rodb,$res,'rowers');
output_json($result);
$rodb->close();
