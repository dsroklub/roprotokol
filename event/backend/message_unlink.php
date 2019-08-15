<?php

include("../../rowing/backend/inc/common.php");
include("utils.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$msg=json_decode($data);
$message='';
error_log("MESSAGE UNLINK ".print_r($msg,true));

$stmt = $rodb->prepare(
        "DELETE FROM member_message
         WHERE 
         message=? AND member IN
         (SELECT Member.id FROM Member WHERE MemberId=?)") or dbErr($rodb,$res,"Error msg unlink prep: ");

$stmt->bind_param('ss',$msg->id,$cuser) ||  dbErr($rodb,$res,"Error msg unlink bind: ");

$stmt->execute() || dbErr($rodb,$res,"Error msg unlink error: ");
invalidate("message");
echo json_encode($res);
