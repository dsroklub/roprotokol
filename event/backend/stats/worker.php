<?php

include("../../../rowing/backend/inc/common.php");
include("utils.php");

$worker=$_GET["worker"];
// assert($cuser=="baadhal" || $cuser=="7843");
$s="SELECT JSON_OBJECT(
    'id',worklog.id,
    'start_time',JSON_OBJECT('year',YEAR(start_time),'month',MONTH(start_time),'day',DAY(start_time),'hour',HOUR(start_time),'minute',MINUTE(start_time)),
    'end_time',JSON_OBJECT('year',YEAR(worklog.end_time),'month',MONTH(worklog.end_time),'day',DAY(worklog.end_time),'hour',HOUR(worklog.end_time),'minute',MINUTE(worklog.end_time)),
    'hours',hours,
    'task',task,
    'boat',boat,
    'created',worklog.created,
    'work',work
    ) as json
    FROM worklog, Member
    WHERE Member.MemberID=? AND Member.id=worklog.member_id
    ORDER BY start_time";

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mystats $q");
$stmt->bind_param("s",$worker);
$stmt->execute() ||  dbErr($rodb,$res,"worker $q");
$result= $stmt->get_result();
output_json($result);
$stmt->close();
$rodb->close();
