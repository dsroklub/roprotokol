<?php
include("inc/common.php");
include("inc/utils.php");
$sql="
SELECT DISTINCT CONCAT(FirstName,' ',LastName) as name, workertype,argument as winteradmin,Member.MemberId as worker_id,requirement, requirement,IFNULL(MAX(worklog.start_time),'x') as start_time,
 CONVERT(IFNULL(SUM(wl.hours),0),DOUBLE) as allhours
FROM Member LEFT JOIN worker on Member.id=worker.member_id LEFT JOIN MemberRights ON MemberRights.member_id=Member.id AND MemberRight='admin' AND argument='vedligehold'
LEFT JOIN worklog ON worklog.member_id=Member.id AND DATE(worklog.start_time)=DATE(NOW()) AND $workseason
LEFT JOIN worklog wl ON wl.member_id=Member.id AND ((YEAR(wl.start_time)=YEAR(NOW()) AND (MONTH(NOW())<11 OR MONTH(wl.start_time)>10)) OR (YEAR(wl.start_time)=YEAR(NOW())-1 AND MONTH(wl.start_time)>10 AND MONTH(NOW())<11))
WHERE Member.id=worker.member_id AND description='vintervedligehold' AND Member.RemoveDate IS NULL AND worker.season=$workyear
GROUP BY Member.id ORDER BY name";
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_rows($result);
$rodb->close();
