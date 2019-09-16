<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

if (isset($_GET["delete"])) {
    $dsql="
DELETE FROM worker
WHERE assigner='vedligehold'
";

    $stmt = $rodb->prepare($dsql) or dbErr($rodb,$res,"worker del");
    $stmt->execute() ||  dbErr($rodb,$res,"rower work delete");
}


if (isset($_GET["generate"])) {
    $gsql="
INSERT INTO worker(member_id,assigner,requirement,description)
SELECT Member.id,'vedligehold',LEAST(SUM(Meter)/1000/20,25) as req, 'bådvedligehold' as description
FROM Member,Trip,TripMember
WHERE Member.id=TripMember.member_id AND TripMember.TripID=Trip.id AND YEAR(OutTime)=YEAR(NOW())
GROUP BY Member.id
HAVING SUM(Meter)/1000>100
";

    $stmt = $rodb->prepare($gsql) or dbErr($rodb,$res,"worker set");
    $stmt->execute() ||  dbErr($rodb,$res,"rower work set");
}


// FIXME validate
$sql="
SELECT CONCAT(FirstName,' ',LastName) as name, Member.MemberId as id,requirement, worklog.end_time, description, CAST(requirement as FLOAT) as requirement
FROM Member LEFT JOIN worker on Member.id=worker.member_id LEFT JOIN worklog ON worklog.member_id=worker.member_id AND worklog.end_time IS NULL
WHERE Member.id=worker.member_id AND worker.assigner='vedligehold'";
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_rows($result);
$rodb->close();
