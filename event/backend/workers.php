<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

if (isset($_GET["generate"])) {
    $gsql="
INSERT INTO worker(member_id,requirement,description)
SELECT Member.id, LEAST(SUM(Meter)/1000/20,25) as req, 'bådvedligehold' as description
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
SELECT requirement, end_time,description,requirement
FROM Member LEFT JOIN worker on Member.id=worker.member_id
WHERE Member.id=member_id AND worker.assigner='vedligehold'";
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"worker");
$stmt->execute() ||  dbErr($rodb,$res,"rower workers");
$result= $stmt->get_result() or dbErr($rodb,$res,"w");
output_rows($result);
$rodb->close();
