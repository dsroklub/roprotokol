<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");

error_log($data);
$reg=json_decode($data);
$activities=implode(",",$reg->act);
error_log("act= $activities");

$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("INSERT INTO team_requests (date_enter, preferred_time, preferred_intensity, comment, activities, phone,email,member_id) SELECT NOW(),?,?,?,?,?,?,Member.id FROM Member Where MemberID=?")) {
    $stmt->bind_param('sssssss',
    $reg->preferred_time,
    $reg->preferred_intensity,
    $reg->comment,
    $activities,    
    $reg->phone,
    $reg->email,
    $reg->aspirant->id
    );
    if (!$stmt->execute()) {
        error_log("OOOP ".$rodb->error);
        $res['status']=$rodb->error;
        http_response_code(500);
    }
    error_log("did exe");
    invalidate("cox");
    $rodb->close();
} else {
    error_log("request cox error: ".$rodb->error);
    $res['status']=$rodb->error;
    http_response_code(500);
}
echo json_encode($res);
?> 
