<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol","trip"]]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction();
($stmt = $rodb->prepare("UPDATE Destination SET ExpectedDurationNormal=?, ExpectedDurationInstruction=? WHERE Name=? AND Location=?")) || dbErr($rodb,$res,"cannot set duration (Prepare)");
$stmt->bind_param('ddss', $data->duration,$data->duration_instruction ,$data->name,$data->location) || dbErr($rodb,$res,"cannot set duration (bind)");
$stmt->execute() || dbErr($rodb,$res,"cannot set duration");
$rodb->commit();
$rodb->close();
invalidate('destination');
invalidate('boat');
echo json_encode($res);
