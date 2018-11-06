<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction(); // 
error_log('add right '.json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO BoatRights (boat_type,required_right,requirement ) VALUES (?,?,?)")) {
    $stmt->bind_param('sss', $data->boat_type->name,$data->right,$data->subject);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
invalidate('member');
echo json_encode($res);
