<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction();
error_log('add rower right right '.json_encode($data));

error_log('id='.$data->rower->id);
error_log('right'.$data->right->member_right);

if ($stmt = $rodb->prepare("INSERT INTO  MemberRights (member_id,MemberRight,Acquired) SELECT id,?,NOW() FROM Member Where MemberID=?")) {
    $stmt->bind_param('si', $data->right->member_right,$data->rower->id);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
