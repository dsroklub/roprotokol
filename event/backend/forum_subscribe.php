<?php
include("../../rowing/backend/inc/common.php");
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$subscription=json_decode($data);
$message='';
error_log("\n FORUM SUBS".print_r($subscription,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
if ($stmt = $rodb->prepare(
    "INSERT INTO forum_subscription(member,forum,role)
         SELECT Member.id ,forum.name, IF(forum.is_open>0,'member','supplicant')
         FROM Member, forum
         WHERE
           forum.name=? AND
           MemberId=?
     ON DUPLICATE KEY UPDATE role='member'
         ")) {
    $stmt->bind_param(
        'ss',
        $subscription->forum->forum,
        $cuser) ||  die("forum subscribe BIND errro ".mysqli_error($rodb));
    if (!$stmt->execute()) {
        $error=" forum sub exe ".mysqli_error($rodb);
        $message=$message."\n"."forum sub insert error: ".mysqli_error($rodb);
    }
} else {
    $error=" forum sub prep ".mysqli_error($rodb);
}
if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("fora");
invalidate("message");
echo json_encode($res);
