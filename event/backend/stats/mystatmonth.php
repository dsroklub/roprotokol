<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");

$boatclause="";

$s="select Week(OutTime) as week, YEAR(OutTime) AS year,CAST(SUM(Meter) AS UNSIGNED) as distance 
FROM Trip,TripMember,Member WHERE TripMember.TripID=Trip.id AND Member.id=TripMember.member_id AND Member.MemberID=? 
GROUP BY year,week ORDER BY year,week";

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"rower stat month P");
$stmt->bind_param("s",$cuser);
$stmt->execute() || dbErr($rodb,$res,"my stat month"); 
$result= $stmt->get_result();
output_rows($result);
$rodb->close();

