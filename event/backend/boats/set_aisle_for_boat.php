<?php
include("../inc/common.php");
include("../inc/utils.php");
//$vr=verify_right(["admin"=>["roprotokol","boat"]]);
$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction();
$stmt = $rodb->prepare("UPDATE Boat set placement_aisle=? Where id=?") or dbErr($rodb,$res,"set aisle for boat prep");
$stmt->bind_param('ii', $data->placement_aisle,$data->id) || dbErr($rodb,$res,"set aisle for boat bind");
user_log("ændrede porten for en båden ".$data->name);
$stmt->execute() || dbErr($rodb,$res,"set aisle for boat");
error_log("");
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
