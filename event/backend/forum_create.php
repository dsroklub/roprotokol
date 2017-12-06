<?php
include("../../rowing/backend/inc/common.php");
include("inc/forummail.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newforum=json_decode($data);
$message='';
error_log("NEW FORUM: ".print_r($newforum,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
// $cuser="7854"; // FIXME

if ($stmt = $rodb->prepare(
    "INSERT INTO forum (name,description,is_open,is_public,owner) 
   SELECT ?,?,?,?,Member.id FROM Member,MemberRights WHERE 
   Member.MemberId=? AND
   MemberRights.member_id=Member.id AND
   MemberRights.MemberRight='event' AND
   MemberRights.argument='fora' LIMIT 1
"
)) {

    $triptype="NULL";
    $isopen=empty($newforum->is_open)?0:1;
    $ispublic=empty($newforum->is_public)?0:1;
    $stmt->bind_param(
        'ssiis',
        $newforum->forum,
        $newforum->description,
        $isopen,
        $ispublic,
        $cuser
    ) ||  die("create forum BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" forum exe error " . mysqli_error($rodb) . " \nnewforum=".print_r($newforum,true);
        error_log($error);
        $message=$message."\n"."create forum insert error: ".mysqli_error($rodb);
    }
} else {
    $error=" forum db error ".mysqli_error($rodb);
    error_log($error);
}

if (!$ispublic) {
    if ($stmt = $rodb->prepare(
        "INSERT INTO forum_subscription(member,forum,role)
         SELECT Member.id ,forum.name, 'owner'
         FROM Member, forum
         WHERE 
           forum.name=? AND
           MemberId=?
         ")) {

    $stmt->bind_param(
        'ss',
        $newforum->forum,
        $cuser) ||  die("create forum BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" forum sub exe ".mysqli_error($rodb);
        $message=$message."\n"."forum sub insert error: ".mysqli_error($rodb);
    } 
} else {
    $error=" forum sub prep ".mysqli_error($rodb);
}

}

    
if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
invalidate("fora");
echo json_encode($res);
?> 
