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
error_log('right'.$data->right);

if ($stmt = $rodb->prepare("DELETE FROM MemberRights WHERE MemberRight=? AND member_id IN (SELECT id FROM Member WHERE MemberID=?)")) {
    $stmt->bind_param('si', $data->right,$data->rower->id);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('member');
echo json_encode($res);
?> 
