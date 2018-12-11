<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
//error_log($data);
$reg=json_decode($data);
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
        error_log("OOOPe ".$rodb->error."reg ".print_r($reg,true));
        $res['status']="error";
        $res['message']=$rodb->error;
    }
    invalidate("gym");
    $rodb->close();
} else {
    error_log("OOOPrep ".$rodb->error);
}
echo json_encode($res);
