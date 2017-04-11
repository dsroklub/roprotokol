<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction();
error_log('delete rower right:  '.json_encode($data));

error_log('id='.$data->rower->id);
error_log('right'.$data->right->member_right." -".$data->right->arg);

if ($stmt = $rodb->prepare("DELETE FROM MemberRights WHERE MemberRight=? AND (argument IS NULL OR argument=?) AND member_id IN (SELECT id FROM Member WHERE MemberID=?)")) {
    $stmt->bind_param('sss', $data->right->member_right,$data->right->arg,$data->rower->id);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('member');
echo json_encode($res);
?> 
