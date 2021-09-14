<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol","boat"]]);
$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();

$stmt = $rodb->prepare("UPDATE Boat set boat_type=? WHERE id=?") or dbErr($rodb,$res,"set cat for boat (Prepare)");
$stmt->bind_param('ss', $data->category,$data->id) or dbErr($rodb,$res,"set cat for boat (bind)");
$stmt->execute() or dbErr($rodb,$res,"set cat for boat");
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
