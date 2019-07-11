<?php
require("inc/utils.php");
include("../../rowing/backend/inc/common.php");

verify_real_user("registrere timer");
$data = file_get_contents("php://input");
$d=json_decode($data);

error_log(print_r($d->work,true));

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$forum=$d->forum->forum;
if ($cuser!=$d->forummember) {
    verify_forum_owner($forum);     // FIXME check that cuser is forum owner
}
$stmt=$rodb->prepare("INSERT INTO worklog (forum,workdate,work,hours,member_id,created_by) SELECT ?,?,?,?,mm.id,mc.id FROM Member mc,Member mm WHERE mm.MemberId=? AND mc.MemberId=?")
     or dbErr($rodb,$res,"addwork q");
if (isset($d->work->workdate)) {
  $workdate=date("Y-m-d", strtotime($d->work->workdate));
  } else {
  $workdate=date('Y-m-d');
}
$work="";
if (isset($d->work->done)) {
  $work=$d->work->done;
}
$hours=$d->work->hours;
$forummember=$d->forummember->member_id;
error_log("VALS $forum, wd=$workdate, w=$work, h=$hours,m=$forummember,u=$cuser");
$stmt->bind_param("sssdss", $forum, $workdate, $work, $hours,$forummember,$cuser)  or dbErr($rodb,$res,"addwork b");

$stmt->execute() or dbErr($rodb,$res,"addwork exe");
invalidate("work");
echo json_encode($res);
