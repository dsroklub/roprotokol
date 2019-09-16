<?php
include("../../rowing/backend/inc/common.php");
require("inc/utils.php");

//verify_real_user("registrere timer");
$data = file_get_contents("php://input");
$d=json_decode($data);
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
$stmt->bind_param("sssdss", $d->boat->name,$forum, $d->work, $hours,$d->id,$cuser) || dbErr($rodb,$res,"addwork e");
$stmt->execute() or dbErr($rodb,$res,"addwork exe");
invalidate("work");
echo json_encode($res);
