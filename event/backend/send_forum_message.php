<?php
include("../../rowing/backend/inc/common.php");

include("messagelib.php");
$data = file_get_contents("php://input");

error_log("data: $data");
$msg=json_decode($data);

$toEmails=array();
error_log("forum: " . $msg->forum->forum);

$res=post_forum_message($msg->forum->forum, $msg->subject,  $msg->body);

echo json_encode($res);
?> 
