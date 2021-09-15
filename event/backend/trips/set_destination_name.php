<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$jsondata = file_get_contents("php://input");
$data=json_decode($jsondata);

$rodb->begin_transaction();
error_log("set dist ".json_encode($data));

($stmt = $rodb->prepare("UPDATE Destination SET Name=? WHERE Name=? AND Location=?")) || dbErr($rodb,$res,"cannot set destinanation name");
$stmt->bind_param('sss', $data->name ,$data->orig_name,$data->location) || dbErr($rodb,$res,"cannot set destination name");
$stmt->execute() || dbErr($rodb,$res,"cannot set destination name");
$rodb->commit();
$rodb->close();
invalidate('destination');
echo json_encode($res);

