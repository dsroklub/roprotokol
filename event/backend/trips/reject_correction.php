<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"trip"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log('reject correction:  '.json_encode($data));

if ($stmt = $rodb->prepare("UPDATE Error_Trip SET Fixed=2 WHERE id=?")) {
    $stmt->bind_param('i', $data->correction->id);
    $stmt->execute() || error_log(' error report rejection failed: '.$rodb->error);
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
