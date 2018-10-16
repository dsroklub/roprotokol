<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();

if ($stmt = $rodb->prepare("INSERT INTO TripType (Name,Description,Created,Active,tripstat_name)".
" VALUES (?,?,NOW(),1,?)")) { 
    $stmt->bind_param('sss', $data->name,$data->description,$data->name);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);

