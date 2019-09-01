<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$from="1857-01-01";

if (isset($_GET["from"])) {
    $from=$_GET["from"];
}

$s="SELECT MAX(hours) as h,JSON_MERGE(
    JSON_OBJECT(
      'member_id',Member.MemberID, 
      'name', CONCAT(FirstName,' ',LastName),
      'forum', forum,
      'hours', SUM(hours) 
   ),
   CONCAT( '{', JSON_QUOTE('log'),': [',
     GROUP_CONCAT(JSON_OBJECT(
      'workdate',workdate,
      'hours',hours,
      'by',created_by,
      'boat', boat,
      'created',worklog.created )),
   ']}')
   ) AS json
   FROM Member LEFT JOIN worklog on worklog.member_id=Member.id  
   WHERE Member.MemberID!='0' AND Member.id>=0 AND workdate > ?
   GROUP BY Member.id,forum HAVING h IS NOT NULL;
";

// use: json->>'$.hours' IS NOT NULL from mariadb 10.3
    
if ($sqldebug) {
    echo "f=$from s=$s\n";
}

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"worklog q");
if ($sqldebug) {
    echo "f=$from s=$s\n";
}
$stmt->bind_param('s',$from) or dbErr($rodb,$res,"worklog bind");

$stmt->execute() or dbErr($rodb,$res,"worklog (Exe)");
$result= $stmt->get_result();
output_json($result);
