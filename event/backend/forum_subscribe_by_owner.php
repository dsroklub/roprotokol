<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$subscription=json_decode($data);
$message='';
error_log("member: ".$subscription->member->id);
error_log("forum:". $subscription->forum->forum);
error_log("role:". $subscription->role);

if ($stmt = $rodb->prepare(
    "INSERT INTO forum_subscription(member,forum,role)
         SELECT Member.id, ?,?
         FROM Member
         WHERE 
           MemberId=?
          AND EXISTS (
            SELECT 'x' FROM forum, Member owner WHERE owner.MemberId=? and forum.owner=owner.id AND forum.name=?
          )
        ON DUPLICATE KEY UPDATE role='member'
         ")) {

    $role="member";
    if (!empty($subscription->role)) {
        $role=$subscription->role;
    }
    $stmt->bind_param(
        'sssss',
        $subscription->forum->forum,
        $role,
        $subscription->member->id,        
        $cuser,
        $subscription->forum->forum
        
    ) ||  die("forum member by owner BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" forummember by owner exe ".mysqli_error($rodb);
        $message=$message."\n"."owner forummember error: ".mysqli_error($rodb);
    }
    if ($rodb->affected_rows !=1) {
        $res["status"]="warning";
        $res["warning"]="duplicate";
    }
} else {
    $error=" forummember by owner ".mysqli_error($rodb);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("fora");
echo json_encode($res);
