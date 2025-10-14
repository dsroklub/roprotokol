<?php
include("inc/common.php");
include("inc/utils.php");
$currentClause="";
if (!verify_right(["admin"=>["roprotokol"],"data"=>["stat"]],false)) {
  $currentClause=" AND RemoveDate IS NULL";
  $currentClause=" AND (Member.RemoveDate IS NULL OR Member.RemoveDate>=NOW()) ";
}
$s="SELECT JSON_OBJECT(
      'id',Member.MemberID,
      'phone',member_setting.phone,
      'email_shared',member_setting.email_shared,
      'status', IF(Member.RemoveDate AND Member.RemoveDate<NOW(),'ikke medlem',IF(membertype='passiv','passiv','ok')),
      'name', CONCAT(FirstName,' ',LastName),
      'rights', JSON_ARRAYAGG(JSON_OBJECT('member_right',MemberRight,'arg',argument,'acquired',Acquired,'by',CONCAT(Member.FirstName,' ',Member.LastName)))
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
