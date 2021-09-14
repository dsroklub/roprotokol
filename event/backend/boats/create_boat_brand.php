<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();
error_log("new bt ".json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO boat_brand (name) VALUES (?)")) { 
    $stmt->bind_param('s', $data->name);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('boat');
invalidate('admin');
echo json_encode($res);
