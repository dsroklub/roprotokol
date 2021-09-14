<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$usage=json_decode($data);

$rodb->begin_transaction();
if ($stmt = $rodb->prepare("UPDATE boat_usage SET name=? WHERE id=?")) {
    $stmt->bind_param('si', $usage->name,$usage->id);
    $stmt->execute() |  error_log("usage update name :".$rodb->error);
} else {
    error_log("usage update nm error :".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
