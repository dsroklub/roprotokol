<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$usage=json_decode($data);

$rodb->begin_transaction();
if ($stmt = $rodb->prepare("UPDATE boat_usage SET description=? WHERE id=?")) {
    $stmt->bind_param('si', $usage->description, $usage->id) |  error_log("usage update bind description :".$rodb->error);
    $stmt->execute() |  error_log("usage update description :".$rodb->error);
} else {
    error_log("usage update nm error :".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
