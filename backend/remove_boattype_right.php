<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log('remove right '.json_encode($data));

if ($stmt = $rodb->prepare("DELETE FROM BoatRights WHERE boat_type=? AND required_right=?")) {
    $stmt->bind_param('is', $data->boattype->id,$data->right);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
