<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();
error_log("set dist ".json_encode($data));

if ($stmt = $rodb->prepare("UPDATE Destination SET Meter=? WHERE Name=? AND Location=?")) { 
    $stmt->bind_param('dss', $data->distance,$data->name,$data->location);
    $stmt->execute() || error_log('set dist '.$rodb->error);
} else {
    error_log('SET dist '.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
