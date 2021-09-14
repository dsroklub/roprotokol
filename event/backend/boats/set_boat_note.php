<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);

$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction();

$stmt = $rodb->prepare("UPDATE Boat set note=? Where id=?") or dbErr($rodb,$res,'boat note');
$stmt->bind_param('si', $data->note,$data->id) or dbErr($rodb,$res,'boat note bind');
$stmt->execute() or dbErr($rodb,$res,'boat note exe');
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
