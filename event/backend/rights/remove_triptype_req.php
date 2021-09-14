<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat","admin"=>"right"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction();
error_log('remove right '.json_encode($data));

if ($stmt = $rodb->prepare("DELETE FROM TripRights WHERE trip_type=? AND required_right=? AND requirement=?")) {
    $stmt->bind_param('iss', $data->triptype->id,$data->right->required_right,$data->right->requirement);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
