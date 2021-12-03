<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
//error_log($data);
$reg=json_decode($data);
$res=array ("status" => "ok");
$stmt = $rodb->prepare("INSERT INTO team_participation (team, dayofweek,timeofday,member_id, start_time, classdate) SELECT ?,?,?,id,NOW(),CURDATE() FROM Member WHERE MemberID=?") or dbErr($rodb,$res,"gym registerer");
$stmt->bind_param('ssss',
                  $reg->team->name,
                  $reg->team->dayofweek,
                  $reg->team->timeofday,
                  $reg->member->id
) || dbErr($rodb,$res,"gym registerer");

$stmt->execute() || dbErr($rodb,$res,"gym registerer exe");
invalidate("gym");
$rodb->close();
echo json_encode($res);
