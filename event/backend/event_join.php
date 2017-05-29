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
        "INSERT INTO event_member(member,event,enter_time,role)
         SELECT Member.id, event.id,NOW(),IF(event.open>0,?,'supplicant')
         FROM Member,event
         WHERE 
           event.id=? AND
           MemberId=?
         ")) {

    $role="member";
    if (!empty($subscription->new_role)) {
        $role=$subscription->new_role;
    }
    $stmt->bind_param(
        'sss',
        $role,
        $subscription->event_id,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" event join exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."event join insert error: ".mysqli_error($rodb);
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
