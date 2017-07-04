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

error_log("I am $cuser");
if ($stmt = $rodb->prepare(
        "DELETE FROM event_member
         WHERE 
         event=? AND member IN
         (SELECT Member.id FROM Member WHERE MemberId=?)")) {

    $stmt->bind_param(
        'ss',
        $subscription->event_id,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" event leave exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."event join insert error: ".mysqli_error($rodb);
    } 
} else {
    $error=" event leave st ".mysqli_error($rodb);
    error_log($error);
}
if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("forum");
echo json_encode($res);
