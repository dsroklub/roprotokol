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

if ($stmt = $rodb->prepare(
        "DELETE FROM member_message
         WHERE 
         message=? AND member IN
         (SELECT Member.id FROM Member WHERE MemberId=?)")) {

    $stmt->bind_param(
        'ss',
        $msg->id,
        $cuser) ||  die("msg unlink BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" msg unlink ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"." msg unlink error: ".mysqli_error($rodb);
    } 
} else {
    $error=" msg unlink ".mysqli_error($rodb);
    error_log($error);
}
if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("message");
echo json_encode($res);
