<?php
include("../../rowing/backend/inc/common.php");
require("inc/utils.php");
include("messagelib.php");
error_log("addwork cuser $cuser");
//if ($cuser!='baadhal') {
//    roErr("man kan kun skrive sig i bådhallen ved kontoret");
//}
//verify_real_user("registrere timer");
$data = file_get_contents("php://input");
$d=json_decode($data);

if ($cuser == "baadhal" ) {
    $checkstmt=$rodb->prepare("SELECT start_time FROM worklog,Member WHERE Member.id=worklog.member_id AND Member.MemberId=? AND end_time IS NULL") or dbErr($rodb,$res,"addwork chk");
    $checkstmt->bind_param("s", $d->worker_id) || dbErr($rodb,$res,"addwork check e");
    $checkstmt->execute() or dbErr($rodb,$res,"check addwork exe");
    $existing= $checkstmt->get_result() or dbErr($rodb,$res,"w");
    if ($e=$existing->fetch_assoc()) {
        dbErr($rodb,$res,"allerede checket ind men ikke ud");
    }
}

$stmt=$rodb->prepare("INSERT INTO worklog (boat,forum,start_time,work,hours,member_id,created_by)
  SELECT ?,?,NOW(),?,?,mm.id,mc.id FROM Member mc,Member mm WHERE mm.MemberId=? AND mc.MemberId=?")
     or dbErr($rodb,$res,"addwork prep");
if (isset($d->start_time)) {
    $start_time=date("Y-m-d H:i:s", strtotime($d->start_time));
} else {
    $start_time=date("Y-m-d H:i:s");
}
$hours=$d->hours ?? null;
$forum=$d->forum ?? null;
$boatName=null;
$stmt->bind_param("sssdss", $boatName,$forum, $d->work, $hours,$d->worker_id,$cuser) || dbErr($rodb,$res,"addwork e");
$stmt->execute() or dbErr($rodb,$res,"addwork exe");
$rd=$rodb->query("SELECT LAST_INSERT_ID() as work_id FROM DUAL")->fetch_assoc() or dbErr($rodb,$res,"work last id");
$work_id=$rd["work_id"];
$res["work_id"]=$work_id;
$res["hours"]=$hours;
invalidate("work");

echo json_encode($res);
