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
    "UPDATE FROM forum_subscription
        SET role='member'
         WHERE forum=? AND 
           (member IN (SELECT Member.id FROM Member WHERE MemberId=?) AND forum.owner IN (SELECT Member.id FROM Member WHERE MemberId=?))"
)
     ) {
        $stmt->bind_param(
            'sss',
            $subscription->name,
            $subscription->member,
            $cuser) ||  die("accept supplicant BIND errro ".mysqli_error($rodb));

        if ($stmt->execute()) {
            $res['status']='ok';
        } else {
            $error=" forum supplicant exe ".mysqli_error($rodb);
            error_log($error);
            $message=$message."\n"."accept forum supplicant error: ".mysqli_error($rodb);
        }
} else {
    $error=" accept supplicant prepare error".mysqli_error($rodb);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("fora");
echo json_encode($res);
?> 
