<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

if (false && isset($_GET["delete"])) {
    verify_right("admin","vedligehold");
    $dsql="
DELETE FROM worker
WHERE assigner='vedligehold'
";
    $stmt = $rodb->prepare($dsql) or dbErr($rodb,$res,"worker del");
    $stmt->execute() ||  dbErr($rodb,$res,"rower work delete");
}


// if (isset($_GET["generate"])) {
//    verify_right("admin","vedligehold");
//     $gsql="
// INSERT INTO worker(member_id,assigner,requirement,description)
// SELECT Member.id,'vedligehold',LEAST(SUM(Meter)/1000/20,25) as req, 'bådvedligehold' as description
// FROM Member,Trip,TripMember
// WHERE Member.id=TripMember.member_id AND TripMember.TripID=Trip.id AND YEAR(OutTime)=YEAR(NOW())
// GROUP BY Member.id
// HAVING SUM(Meter)/1000>100
// ";

//     $stmt = $rodb->prepare($gsql) or dbErr($rodb,$res,"worker set");
//     $stmt->execute() ||  dbErr($rodb,$res,"rower work set");
// }
// FIXME validate

$sql="
SELECT DISTINCT CONCAT(FirstName,' ',LastName) as name, workertype,argument as winteradmin,Member.MemberId as worker_id,requirement, requirement,IFNULL(MAX(worklog.start_time),'x') as start_time
FROM Member LEFT JOIN worker on Member.id=worker.member_id LEFT JOIN MemberRights ON MemberRights.member_id=Member.id AND MemberRight='admin' AND argument='vedligehold'
LEFT JOIN worklog ON worklog.member_id=Member.id AND DATE(worklog.start_time)=DATE(NOW())
WHERE Member.id=worker.member_id AND worker.assigner='vedligehold' AND Member.RemoveDate IS NULL
GROUP BY Member.id ORDER BY name";
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_rows($result);
$rodb->close();
