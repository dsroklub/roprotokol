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
error_log("Sticky MESSAGE UNLINK ".print_r($msg,true));

$stmt = $rodb->prepare("DELETE FROM forum_message WHERE sticky>0 AND forum_message.id=?") or dbErr($rodb,$res,"Error sticky msg unlink prep: ");

$stmt->bind_param('i',$msg->id) ||  dbErr($rodb,$res,"Error sticky msg unlink bind: ");
$stmt->execute() || dbErr($rodb,$res,"Error sticky msg unlink error: ");
invalidate("messages");
echo json_encode($res);
