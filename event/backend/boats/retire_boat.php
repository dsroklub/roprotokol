<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);
$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction();
error_log("retire boat ".json_encode($data));
if ($stmt = $rodb->prepare("UPDATE Boat SET Decommissioned=NOW(),Location=NULL,placement_aisle=NULL,placement_level=NULL,placement_row=NULL,placement_side=NULL WHERE Name=?")) {
    $stmt->bind_param('s', $data->name);
    $stmt->execute();
} else {
    error_log("OOOP ".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
