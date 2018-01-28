<?php
include("../../rowing/backend/inc/common.php");

include("messagelib.php");
$data = file_get_contents("php://input");

error_log("private message data: $data");
$msg=json_decode($data);

$toEmails=array();
error_log("send private message: " . $msg->member->id);
error_log("pm ".print_r($msg,true));
$res=post_private_message($msg->member->id, $msg->subject,  $msg->body);
echo json_encode($res);
