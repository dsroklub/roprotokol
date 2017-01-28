<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);
error_log($reg->team->name);
error_log($reg->member->id);


$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("
INSERT INTO team_participation (team, dayofweek,timeofday,member_id, start_time, classdate) SELECT ?,?,?,id,NOW(),CURDATE() FROM Member WHERE MemberID=?")) {
    $stmt->bind_param('ssss',
    $reg->team->name,
    $reg->team->dayofweek,
    $reg->team->timeofday,
    $reg->member->id
    );
    if (!$stmt->execute()) {
        error_log("OOOP ".$rodb->error);
        $res['status']=$rodb->error;
    }
    error_log("did exe");
    invalidate("gym");
    $rodb->close();
} else {
    error_log("OOOP ".$rodb->error);
}
echo json_encode($res);
?> 
