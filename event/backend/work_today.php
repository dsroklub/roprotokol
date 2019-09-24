<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$sql="
SELECT CONCAT(FirstName,' ',LastName) as name, Member.MemberId as worker_id,requirement, DATE_FORMAT(worklog.start_time,'%Y-%m-%dT%T') as start_time, hours,worklog.boat,worklog.work,DATE_FORMAT(worklog.end_time,'%Y-%m-%dT%T') AS end_time, description,task
FROM Member, worker, worklog 
WHERE 
  Member.id=worker.member_id AND worker.assigner='vedligehold' AND
  worklog.member_id=worker.member_id AND (worklog.end_time IS NULL OR DATE(worklog.end_time)=DATE(NOW()))
";
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_rows($result);
$rodb->close();
