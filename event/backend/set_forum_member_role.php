<?php
include("../../rowing/backend/inc/common.php");

$data = file_get_contents("php://input");
error_log("SET forum member role $data\n");
$forummember=json_decode($data);

$stmt = $rodb->prepare(
        "UPDATE forum_subscription
         SET role=?
         WHERE forum=? AND member IN (SELECT Member.id FROM Member WHERE MemberId=?) AND
         EXISTS (SELECT 'x' FROM forum,Member me where forum.owner=me.id and me.MemberID=?)
") or dbErr($rodb,$res,"set forum member role");
$stmt->bind_param(
        'ssss',
        $forummember->role,
        $forummember->forum,
        $forummember->member_id,
        $cuser
) ||  dbErr($rodb,$res,"set forum member role BIND errro ");

$stmt->execute() ||  dbErr($rodb,$res,"set forum member role BIND");

invalidate("fora");
invalidate("message");
echo json_encode($res);
