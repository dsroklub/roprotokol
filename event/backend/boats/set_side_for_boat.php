<?php
include("inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);
$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
error_log("set side ".json_encode($data));
$rodb->begin_transaction();
if ($stmt = $rodb->prepare("UPDATE Boat set placement_side=? Where id=?")) {
    $stmt->bind_param('si', $data->placement_side,$data->id);
    $stmt->execute();
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
