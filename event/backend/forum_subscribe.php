<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$subscription=json_decode($data);
$message='';
error_log(print_r($subscription,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
        "INSERT INTO forum_subscription(member,forum,role)
         SELECT Member.id, ?,?
         FROM Member
         WHERE 
           MemberId=?
         ")) {

    $triptype="NULL";
    $stmt->bind_param(
        'sss',
        $subscription->forum->name,
        $subscription->role,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" event exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create event insert error: ".mysqli_error($rodb);
    } 
}



if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("forum");
echo json_encode($res);
?> 
