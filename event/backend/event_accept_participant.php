<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$participation=json_decode($data);
$message='';

error_log(print_r($participation,true));

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
    "UPDATE event_member
     SET role='member'
         WHERE event=? AND
           (event_member.member IN (SELECT Member.id FROM Member WHERE MemberId=?) AND
             EXISTS (SELECT 'x' FROM event, Member owner WHERE owner.id=event.owner AND owner.MemberId=?))"
      )
     ) {
        $stmt->bind_param(
            'sss',
            $participation->event_id,
            $participation->member_id,
            $cuser) ||  die("event member accept BIND errro ".mysqli_error($rodb));
    }

if ($stmt) {
    if ($stmt->execute()) {
        $res['status']='ok';
    } else {
        $error=" event member accept ".mysqli_error($rodb);
        $message=$message."\n"."event rm member error: ".mysqli_error($rodb);
    }
} else {
    $error=" event accept member ".mysqli_error($rodb);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("fora");
echo json_encode($res);
