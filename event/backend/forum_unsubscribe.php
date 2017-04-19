<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$subscription=json_decode($data);
$message='';
error_log("\nUNSUBSCRIBE $data\n");

error_log(print_r($subscription,true));

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
        "DELETE FROM forum_subscription
         WHERE forum=? AND member IN (SELECT Member.id FROM Member WHERE MemberId=?)")) {

    $stmt->bind_param(
        'ss',
        $subscription->name,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" event exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create event insert error: ".mysqli_error($rodb);
    } else {
        error_log("unsubscribe OK");
    }
} else {
    $error=" event prepare ".mysqli_error($rodb);
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
?> 
