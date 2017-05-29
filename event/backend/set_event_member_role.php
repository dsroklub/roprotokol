<?php
error_log("ROLE\n");
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("SET member role $data\n");
$eventmember=json_decode($data);
$message='set role ';

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
        "UPDATE event_member
         SET role=?
         WHERE event=? AND member IN (SELECT Member.id FROM Member WHERE MemberId=?) AND
         EXISTS (SELECT 'x' FROM event,Member me where event.owner=me.id and me.MemberID=?)
"
)
) {

    $stmt->bind_param(
        'ssss',
        $eventmember->new_role,
        $eventmember->event,
        $eventmember->member_id,
        $cuser
    ) ||  die("create event BIND errro ".mysqli_error($rodb));

    if ($stmt->execute()) {
        error_log("member role set OK");
    } else {
        $error=" member role set exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."role update error: ".mysqli_error($rodb);
    }
} else {
    $error=" member role set ".mysqli_error($rodb);
    error_log($error);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
error_log(print_r($res,true));
echo json_encode($res);
?> 
