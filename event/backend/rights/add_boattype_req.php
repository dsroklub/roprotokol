<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat","admin"=>"right"]);
$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction(); // 
error_log('add right '.json_encode($data));
$stmt = $rodb->prepare  ("INSERT INTO BoatRights (boat_type,required_right,requirement ) VALUES (?,?,?)") or dbErr($rodb,$res,"BT req (prep)");
$stmt->bind_param('sss', $data->boat_type->name,$data->right,$data->subject) or dbErr($rodb,$res,"BT req (bind)");
$stmt->execute() or dbErr($rodb,$res,"BT req");
$rodb->commit();
$rodb->close();
invalidate('boat');
invalidate('member');
echo json_encode($res);
