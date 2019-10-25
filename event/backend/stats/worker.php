<?php

include("../../../rowing/backend/inc/common.php");
include("utils.php");

$worker=$_GET["worker"];
assert($cuser=="baadhal" || $cuser="7843");
$s="SELECT DATE_FORMAT(start_time,'%Y-%m-%dT%T') as start_time,DATE_FORMAT(end_time,'%Y-%m-%dT%T') as end_time,hours,task,boat,worklog.created,work
    FROM worklog, Member
    WHERE Member.MemberID=? AND Member.id=worklog.member_id
    ORDER BY start_time";

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mystats $q");
$stmt->bind_param("s",$worker);
$stmt->execute() ||  dbErr($rodb,$res,"worker $q");
$result= $stmt->get_result();
output_rows($result);
$stmt->close();
$rodb->close();
