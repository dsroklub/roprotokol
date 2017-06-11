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


if ($cuser==$subscription->member_id) {
    error_log("self DEL");
    if ($stmt = $rodb->prepare(
        "DELETE FROM forum_subscription
         WHERE forum=? AND 
           member IN (SELECT Member.id FROM Member WHERE MemberId=?)"
      )
     ) {
        $stmt->bind_param(
            'ss',
            $subscription->forum,
            $cuser) ||  die("forum unsub BIND errro ".mysqli_error($rodb));
    }
    
} else {
    error_log("DEL by owner");
    if ($stmt = $rodb->prepare(
        "DELETE FROM forum_subscription
         WHERE forum=? AND 
           (forum_subscription.member IN (SELECT Member.id FROM Member WHERE MemberId=?) AND 
             EXISTS (SELECT 'x' FROM forum, Member owner WHERE owner.id=forum.owner AND owner.MemberId=?))"
      )
     ) {
        $stmt->bind_param(
            'sss',
            $subscription->forum,
            $subscription->member_id,
            $cuser) ||  die("forum unsubscribe BIND errro ".mysqli_error($rodb));
    }    
}

if ($stmt) {
    if ($stmt->execute()) {
        $res['status']='ok';
    } else {
        $error=" forum unsubscribe ".mysqli_error($rodb);
        $message=$message."\n"."forum unsub error: ".mysqli_error($rodb);
    }
} else {
    $error=" forum unsubscribe prepare ".mysqli_error($rodb);
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
