<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log("activate TT ".json_encode($data));

if ($stmt = $rodb->prepare("UPDATE TripType SET Active=? WHERE id=?")) {
    $stmt->bind_param('ii', $data->active,$data->id);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
