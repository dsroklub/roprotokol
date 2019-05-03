<?php
include("../../rowing/backend/inc/common.php");

include("messagelib.php");
$data = file_get_contents("php://input");

error_log("private message data: $data");
$msg=json_decode($data);

$toEmails=array();
$replyTo="private_noreply";
if ($cuser && $cuser>0) {
  $replyTo="member_$cuser@aftaler.danskestudentersroklub.dk";
}
error_log("send private message to: " . $msg->member->id);
error_log("pm ".print_r($msg,true));
$res=post_private_message($msg->member->id, $msg->subject,  $msg->body, $replyTo);
invalidate("message");
echo json_encode($res);
