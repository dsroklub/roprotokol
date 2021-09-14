<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();

if ($stmt = $rodb->prepare("UPDATE Boat set placement_aisle=? Where id=?")) {
    $stmt->bind_param('ii', $data->placement_aisle,$data->id);
    $stmt->execute();
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
