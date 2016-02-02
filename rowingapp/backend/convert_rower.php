<?php
include("inc/common.php");
include("inc/verify_user.php");
$res=array ("status" => "ok");

$data = file_get_contents("php://input");
$fromto=json_decode($data);
$error=null;
error_log('convertrower '.json_encode($fromto));

$rodb->begin_transaction();

if ($stmt = $rodb->prepare("UPDATE TripMember, Member as fm, Member as tm Set TripMember.member_id = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND TripMember.member_id=fm.id")) {
    $stmt->bind_param('ss', $fromto->to->id,$fromto->from->id) |     error_log("fromto bind error ".$rodb->error);
    if (!$stmt->execute()) {
            $error=$rodb->error;
            error_log($error);
    }
} else {
    error_log("fromto bind error ".$rodb->error);
}

if ($error) {
    error_log('DB error ' . $error);
    $res['message']=$message.'\n'.$error;
    $res['status']='error';
    $res['error']=$error;
    $rodb->rollback();
} else {
    $rodb->commit();
}

invalidate("member");
$rodb->close();
echo json_encode($res);
?> 
