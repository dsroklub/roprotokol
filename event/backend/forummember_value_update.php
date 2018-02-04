<?php
include("../../rowing/backend/inc/common.php");
require("utils.php");
$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$update=json_decode($data,true);
error_log("FM val ".print_r($update,true));
if ($stmt = $rodb->prepare("
 UPDATE forum_subscription
 SET forum_subscription.value=?
 WHERE 
  forum_subscription.forum=? AND
 forum_subscription.member IN (SELECT ms.id from Member ms, Member mo,forum WHERE ms.MemberId=? AND mo.MemberId=? AND mo.id=forum.owner AND forum.name=forum_subscription.forum)
   ")
)  {
    $stmt->bind_param(
        'dsss',
        $update["value"],
        $update["forummember"]["forum"],
        $update["forummember"]["member_id"],
        $cuser) ||  die("member setting BIND errro ".mysqli_error($rodb));
    if (!$stmt->execute()) {
        $error=" forummember upd exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create setting insert error: ".mysqli_error($rodb);
    } 
} else {
        $error=" forummember update exe ".mysqli_error($rodb);
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

