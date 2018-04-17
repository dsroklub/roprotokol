<?php
include("../../rowing/backend/inc/common.php");
require("utils.php");
$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$settings=json_decode($data,true);
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
error_log("MS UPDATE $data,   PUB=".$settings['is_public']);
if ($stmt = $rodb->prepare(
        "INSERT INTO member_setting(member,is_public,show_status,show_activities,notification_email,phone,email_shared)
         SELECT Member.id, ?,?,?,?,?,?
         FROM Member
         WHERE 
           MemberId=?
         ON DUPLICATE KEY 
  UPDATE is_public=VALUES(is_public),show_status=VALUES(show_status),show_activities=VALUES(show_activities),notification_email=VALUES(notification_email),phone=VALUES(phone),email_shared=VALUES(email_shared)
         "))  {
    $notification_email=null;
    $phone=null;
    $is_public=0;
    if (!empty($settings['is_public']) ) {
        $is_public= $settings['is_public'];
    }
    $show_status=0;
    $show_activities=0;
    if (!empty($settings['show_status'])) {
        $show_status=$settings['show_status'];
    }
    if (!empty($settings['show_activities'])) {
        $show_activities=$settings['show_activities'];
    }
    if (!empty($settings['notification_email'])) {
        $notification_email= filter_var($settings['notification_email'],FILTER_SANITIZE_EMAIL);
    }
    if (!empty($settings['phone'])) {
        $phone=sanestring(trim($settings['phone']),false,"+ 01234567890");
    }
    if (!empty($settings['email_shared'])) {
        $email_shared= filter_var($settings['email_shared'],FILTER_SANITIZE_EMAIL);
    }
    $stmt->bind_param(
        'sssssss',
        $is_public,
        $show_status,        
        $show_activities,
        $notification_email,
        $phone,
        $email_shared,
        $cuser) ||  die("member setting BIND errro ".mysqli_error($rodb));
    if (!$stmt->execute()) {
        $error=" member setting exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create setting insert error: ".mysqli_error($rodb);
    } 
} else {
        $error=" mem setting update exe ".mysqli_error($rodb);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("fora");
invalidate("member");
echo json_encode($res);
?> 

