<?php
include("../../rowing/backend/inc/common.php");

include("messagelib.php");
$data = file_get_contents("php://input");
$msg=json_decode($data);
error_log("send forum message: " . $msg->forum->forum);
$res=post_forum_message($msg->forum->forum, $msg->subject,  $msg->body,$from=null,$forumEmail=null,$msg->sticky ?? null);
if (!empty($msg->replace)) {
    $stmt = $rodb->prepare("UPDATE forum_message SET deleted=NOW() WHERE id=?") or dbErr($rodb,$res,"send forum msg");
    $stmt->bind_param("i",$msg->replace)  or dbErr($rodb,$res,"replace msg");
    $stmt->execute() or dbErr($rodb,$res,"replace MSG");
}
echo json_encode($res);
