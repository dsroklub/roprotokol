<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol","trip","right"]]);
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction();
//error_log('add trip right '.json_encode($data));
$stmt = $rodb->prepare("INSERT INTO  TripRights (trip_type,required_right,requirement ) VALUES (?,?,?)") or dbErr($rodb,$res,"create trip req");
$stmt->bind_param('iss', $data->triptype->id,$data->right,$data->subject) || dbErr($rodb,$res,"create trip req b");
$stmt->execute() || dbErr($rodb,$res,"create trip req X");
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
