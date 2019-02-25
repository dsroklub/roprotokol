<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$res=array ("status" => "ok");

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
      'created',worklog.created )),
   ']}')
   ) AS json
   FROM Member LEFT JOIN worklog on worklog.member_id=Member.id  
   WHERE Member.MemberID!='0' AND Member.id>=0 AND workdate > ?
   GROUP BY Member.id,forum HAVING h is NOT NULL;
";

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


echo '[';
$first=1;
while ($row = $result->fetch_assoc()) {
    if ($first) $first=0; else echo "\n,";	  
    echo $row['json'];
}
echo ']';

