<?php
error_log("ROLE\n");
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("SET forum member role $data\n");
$forummember=json_decode($data);
$message='set role ';

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
        "UPDATE forum_subscription
         SET role=?
         WHERE forum=? AND member IN (SELECT Member.id FROM Member WHERE MemberId=?) AND
         EXISTS (SELECT 'x' FROM forum,Member me where forum.owner=me.id and me.MemberID=?)
"
)
) {

    $stmt->bind_param(
        'ssss',
        $forummember->role,
        $forummember->forum,
        $forummember->member_id,
        $cuser
    ) ||  die("set forum member role BIND errro ".mysqli_error($rodb));
    if ($stmt->execute()) {
        error_log("forum member role set OK");
    } else {
        $error=" forum member role set exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."forum member role update error: ".mysqli_error($rodb);
    }
} else {
    $error=" forum member role set ".mysqli_error($rodb);
    error_log($error);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("forum");
invalidate("message");
error_log(print_r($res,true));
echo json_encode($res);
?> 
