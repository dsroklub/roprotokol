<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();
error_log("new bt ".json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO TripType (Name,Description,Created,Active) ".
" VALUES (?,?,NOW(),1)")) { 
    $stmt->bind_param('ss', $data->name,$data->description);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
