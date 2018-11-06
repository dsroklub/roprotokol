<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log('remove right '.json_encode($data));

if ($stmt = $rodb->prepare("DELETE FROM BoatRights WHERE boat_type=? AND required_right=? AND requirement=?")) {
    $stmt->bind_param('sss', $data->boat_type->name,$data->right->required_right,$data->right->requirement);
    $stmt->execute() or dbErr($rodb,$res,"remove boattype");
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
