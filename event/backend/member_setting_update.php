<?php
include("../../rowing/backend/inc/common.php");

$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$settings=json_decode($data,true);
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
error_log("MS UPDATE $data,   PUB=".$settings['is_public']);
if ($stmt = $rodb->prepare(
        "INSERT INTO member_setting(member,is_public,show_status)
         SELECT Member.id, ?,?
         FROM Member
         WHERE 
           MemberId=?
         ON DUPLICATE KEY UPDATE is_public=VALUES(is_public),show_status=VALUES(show_status)

         "))  {
// ON DUPLICATE KEY UPDATE is_public=VALUE(is_public),show_status=VALUE(show_status)

    $is_public=0;
    if (!empty($settings['is_public']) ) {
        $is_public= $settings['is_public'];
    }
    $show_status=0;
    if (!empty($settings['show_status'])) {
        $show_status=$settings['show_status'];
    }
    $show_activities=0;
    if (!empty($settings['show_activities'])) {
        $show_status=$settings['show_activities'];
    }
    $stmt->bind_param(
        'sss',
        $is_public,
        $show_status,
        $cuser) ||  die("member setting BIND errro ".mysqli_error($rodb));
    if (!$stmt->execute()) {
        $error=" setting exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create setting insert error: ".mysqli_error($rodb);
    } 
} else {
        $error=" setting update exe ".mysqli_error($rodb);
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

