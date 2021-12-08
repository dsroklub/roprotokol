<?php
include("inc/common.php");
include("inc/utils.php");

$timeclause="(worklog.end_time IS NULL OR DATE(worklog.start_time)=DATE(NOW()))";

if (isset($_GET["days"])) {
    $timeclause="DATE_ADD(start_time,INTERVAL 1 WEEK) < NOW() ";
    }

$sql="
SELECT JSON_OBJECT(
  'id',id,
  'name',CONCAT(FirstName,' ',LastName),
  'worker_id',MemberId,
  'requirement',requirement,
   'start_time',JSON_OBJECT('year',YEAR(start_time),'month',MONTH(start_time),'day',DAY(start_time),'hour',HOUR(start_time),'minute',MINUTE(start_time)),
   'end_time',JSON_OBJECT('year',YEAR(end_time),'month',MONTH(end_time),'day',DAY(end_time),'hour',HOUR(end_time),'minute',MINUTE(end_time)),
   'hours', hours,
   'boat',boat,
   'work',work,
   'description',description,
   'allhours',allhours,
   'task',task) as json
FROM (
SELECT worklog.id,FirstName,LastName,MemberId,requirement,worklog.start_time,worklog.end_time,worklog.hours,worklog.boat,worklog.work,description,CONVERT(IFNULL(SUM(wl.hours),0),DOUBLE) as allhours,worklog.task
FROM Member LEFT JOIN worklog wl ON wl.member_id=Member.id AND ((YEAR(wl.start_time)=YEAR(NOW()) AND (MONTH(NOW())<11 OR MONTH(wl.start_time)>10)) OR (YEAR(wl.start_time)=YEAR(NOW())-1 AND MONTH(wl.start_time)>10 AND MONTH(NOW())<11)), worker, worklog
WHERE
  Member.id=worker.member_id AND worker.assigner='vedligehold' AND
  worklog.member_id=worker.member_id AND $timeclause
  GROUP BY Member.id ORDER BY worklog.start_time
) as w
";

$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_json($result);
$rodb->close();
