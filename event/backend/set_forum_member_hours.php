<?php
error_log("ROLE\n");
include("inc/common.php");
include("inc/user.php");

$data = file_get_contents("php://input");
$forummember=json_decode($data);

check_forum_owner($forummember->forum);

$stmt = $rodb->prepare(
        "UPDATE forum_subscription
         SET work=?
         WHERE forum=? AND member IN (SELECT Member.id FROM Member WHERE MemberId=?)") or dbErr($rodb,$res,"set forum work");

$stmt->bind_param(
        'dss',
        $forummember->work_todo,
        $forummember->forum,
        $forummember->member_id) ||  dbErr($rodb,$res,"member u=$cuser  hours=$forummember->work_todo bind ");

$stmt->execute() || dbErr($rodb,$res,"member hours Exe");

invalidate("fora");
invalidate("message");
echo json_encode($res);
