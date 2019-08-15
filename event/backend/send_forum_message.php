<?php
include("../../rowing/backend/inc/common.php");

include("messagelib.php");
$data = file_get_contents("php://input");

error_log("data: $data");
$msg=json_decode($data);

$toEmails=array();
error_log("send forum message: " . $msg->forum->forum);
$res=post_forum_message($msg->forum->forum, $msg->subject,  $msg->body,$from=null,$forumEmail=null,$msg->sticky ?? null);
echo json_encode($res);
