<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();
error_log("set cat ".json_encode($data));
error_log("set cat for ". $data->category." -- ".$data->id);

if ($stmt = $rodb->prepare("UPDATE Boat set BoatType=(SELECT id FROM BoatType WHERE Name=?) Where id=?")) { 
    $stmt->bind_param('si', $data->category,$data->id);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
