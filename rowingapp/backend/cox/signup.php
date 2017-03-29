<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);
error_log($reg->team->name);
error_log($reg->member->id);

$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("INSERT INTO instruction_team_participation (team, member_id)") {
    $stmt->bind_param('si',
    $reg->team->name,
    $reg->member->id
    );
    if (!$stmt->execute()) {
        error_log("OOOP ".$rodb->error);
        $res['status']=$rodb->error;
    }
    error_log("did exe");
    invalidate("cox");
    $rodb->close();
} else {
    error_log("OOOP ".$rodb->error);
}
echo json_encode($res);
?> 
