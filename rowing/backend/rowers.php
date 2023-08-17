<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

// TODO when we can use Mariadb 10.5 replace with JSON_ARRAYAGG etc
$s="
SELECT
  JSON_OBJECT(
      'id',Member.MemberID,
      'club',Member.club,
      'status', IF(Member.member_type=1,'passiv','ok'),
      'name', CONCAT(Member.FirstName,' ',Member.LastName),
      'rigths', JSON_ARRAYAGG(JSON_OBJECT('member_right',MemberRight,'arg',argument,'acquired',Acquired,'expire',DATE_ADD(Acquired,INTERVAL MemberRightType.validity YEAR),'by',CONCAT(mb.FirstName,' ',mb.LastName)))
   ) AS json
   FROM
      Member LEFT JOIN MemberRights ON MemberRights.member_id=Member.id
        LEFT JOIN MemberRightType ON MemberRights.MemberRight = MemberRightType.member_right AND MemberRights.argument=MemberRightType.arg
        LEFT JOIN Member mb ON mb.id=MemberRights.created_by
   WHERE
     Member.MemberID!='0' AND
     Member.id>=0 AND (Member.RemoveDate IS NULL OR Member.RemoveDate>=NOW()) AND
     (Member.member_type <> -1 OR Member.member_type IS NULL)
   GROUP BY Member.id";

if ($sqldebug) {
    echo $s."\n<br>\n";
}
$result=$rodb->query($s) or dbErr($rodb,$res,"get rowers");
output_json($result);
$rodb->close();
