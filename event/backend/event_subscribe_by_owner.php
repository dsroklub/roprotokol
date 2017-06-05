<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("DATA $data");
$subscription=json_decode($data);
$message='';
error_log("member: ".$subscription->member->id);
error_log("event: ". $subscription->event->name);
error_log("event: ".print_r($subscription->event,true));
error_log("role:". $subscription->role);

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
    "INSERT INTO event_member(member,event,role,enter_time)
         SELECT Member.id, ?,?,NOW()
         FROM Member
         WHERE 
           MemberId=?
          AND EXISTS (
            SELECT 'x' FROM event, Member owner WHERE owner.MemberId=? and event.owner=owner.id AND event.id=?
          )
         ")) {

    $role="member";
    if (!empty($subscription->role)) {
        $role=$subscription->role;
    }
    $stmt->bind_param(
        'isssi',
        $subscription->event->event_id,
        $role,
        $subscription->member->id,        
        $cuser,
        $subscription->event->event_id
        
    ) ||  die("event member by owner BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" eventmember by owner exe ".mysqli_error($rodb);
        $message=$message."\n"."owner eventmember error: ".mysqli_error($rodb);
    } 
} else {
    $error=" eventmember by owner ".mysqli_error($rodb);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
echo json_encode($res);
?> 
