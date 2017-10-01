<?php
include("../../rowing/backend/inc/common.php");
include("inc/forummail.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$forum=json_decode($data);
$message='';
error_log("DELETE FORUM: ".print_r($forum,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare(
    "DELETE FROM forum WHERE forum.name=?
     AND forum.owner IN (SELECT id FROM Member WHERE MemberID=?)
"
)) {

    $stmt->bind_param(
        'ss',
        $forum->forum,
        $cuser
    ) ||  die("delete forum BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" forum delete error " . mysqli_error($rodb) . " \nforum=".print_r($forum,true);
        error_log($error);
        $message=$message."\n"."delete forum insert: ".mysqli_error($rodb);
    }
} else {
    $error=" forum db error ".mysqli_error($rodb);
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
