<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");


$s="SELECT JSON_OBJECT(       
      'worker', JSON_OBJECT('name', CONCAT(FirstName,' ',LastName), 'id', Member.MemberID),
      'forum', forum,
      'hours', hours, 
      'start_time', DATE_FORMAT(start_time,'%Y-%m-%dT%T'),
      'end_time',DATE_FORMAT(end_time,'%Y-%m-%dT%T'),
      'hours',hours,
      'created_by',created_by,
      'boat', boat,
      'created', worklog.created 
   ) AS json
   FROM Member  JOIN worklog on worklog.member_id=Member.id  
   WHERE Member.MemberID!='0' AND Member.id>=0 AND  end_time IS NULL;
";
if ($sqldebug) {
    echo "f=$from s=$s\n";
}
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"workers");
if ($sqldebug) {
    echo "f=$from s=$s\n";
}
$stmt->execute() or dbErr($rodb,$res,"worklog (Exe)");
$result= $stmt->get_result();
output_json($result);
