<?php
include("inc/common.php");
include("inc/utils.php");

$from="1857-01-01";

if (isset($_GET["from"])) {
    $from=$_GET["from"];
}
$limit="";
if (isset($_GET["active"])) {
    $limit=" AND worklog.end_time IS NULL ";
}

$s="SELECT MAX(hours) as h,
    JSON_OBJECT(
      'member_id',Member.MemberID,
      'name', CONCAT(FirstName,' ',LastName),
      'forum', forum,
      'hours', SUM(hours),
      'log',JSON_ARRAYAGG(JSON_OBJECT(
        'start_time',DATE_FORMAT(start_time,'%Y-%m-%dT%T'),
        'end_time',DATE_FORMAT(end_time,'%Y-%m-%dT%T'),
        'hours',hours,
        'by',created_by,
        'boat', boat,
        'created',DATE_FORMAT(worklog.created,'%Y-%m-%dT%T')))
   ) AS json
   FROM Member LEFT JOIN worklog on worklog.member_id=Member.id
   WHERE Member.MemberID!='0' AND Member.id>=0 AND start_time > ? $limit AND $workseason
   GROUP BY Member.id,forum ;
";
// HAVING h IS NOT NULL
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
