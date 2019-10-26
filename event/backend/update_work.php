<?php
require("inc/utils.php");
include("../../rowing/backend/inc/common.php");

//verify_real_user("registrere timer");
$data = file_get_contents("php://input");
$d=json_decode($data);
$stmt=$rodb->prepare("UPDATE worklog 
    SET start_time=?,end_time=?, work=?, boat=?, hours=?, task=?
    WHERE end_time IS NULL AND member_id IN (SELECT id from Member WHERE MemberID=?)"
)  or dbErr($rodb,$res,"updatework q");
if (isset($d->start_time)) {
    $start_time=date("Y-m-d H:i:s", strtotime($d->start_time));
} else {
    $start_time=date("Y-m-d H:i:s");
}
if (isset($d->end_time)) {
    $end_time=date("Y-m-d H:i:s", strtotime($d->end_time));
} else {
    $end_time=date("Y-m-d H:i:s");
}
$work="";
$hours=(strtotime($d->end_time)-strtotime($d->start_time))/3600;
if ($hours>3){
    $hours=3;
}
$res["hours"]=$hours;
//error_log("XXX $d->work, $d->boat, $hours,$d->id");
$stmt->bind_param("ssssdss", $start_time,$end_time,$d->work, $d->boat, $hours,$d->task,$d->worker_id) || dbErr($rodb,$res,"update work e");
$stmt->execute() or dbErr($rodb,$res,"updwork EXE");
invalidate("work");
echo json_encode($res);
