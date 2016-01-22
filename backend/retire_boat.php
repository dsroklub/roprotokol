<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log("retire boat ".json_encode($data));

if ($stmt = $rodb->prepare("UPDATE Boat SET Decommissioned=NOW() WHERE id=?")) {
    $stmt->bind_param('i', $data->id);
    $stmt->execute();
} else {
    error_log("OOOP".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
