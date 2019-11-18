<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$timeclause="(worklog.end_time IS NULL OR DATE(worklog.start_time)=DATE(NOW()))";

if (isset($_GET["days"])) {
    $timeclause="DATE_ADD(start_time,INTERVAL 1 WEEK) < NOW() ";
    }
        
$sql="
SELECT JSON_OBJECT(
  'id',worklog.id,
  'name',CONCAT(FirstName,' ',LastName), 
  'worker_id',Member.MemberId,
  'requirement',requirement,
   'start_time',JSON_OBJECT('year',YEAR(start_time),'month',MONTH(start_time),'day',DAY(start_time),'hour',HOUR(start_time),'minute',MINUTE(start_time)), 
   'end_time',JSON_OBJECT('year',YEAR(worklog.end_time),'month',MONTH(worklog.end_time),'day',DAY(worklog.end_time),'hour',HOUR(worklog.end_time),'minute',MINUTE(worklog.end_time)), 
   'hours', hours,
   'boat',worklog.boat,
   'work',worklog.work,
   'description',description,
   'task',task) as json
FROM Member, worker, worklog 
WHERE 
  Member.id=worker.member_id AND worker.assigner='vedligehold' AND
  worklog.member_id=worker.member_id AND $timeclause
";
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_json($result);
$rodb->close();
