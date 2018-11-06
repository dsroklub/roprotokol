<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$sjondata = file_get_contents("php://input");
$data=json_decode($sjondata);
$rodb->begin_transaction();
error_log("new destination ". print_r($data,true));
($stmt = $rodb->prepare("
       INSERT INTO Destination (Name,Location, ExpectedDurationNormal, ExpectedDurationInstruction,Created,created_by) 
       SELECT?,?,?,?,NOW(),Member.id FROM Member WHERE Member.MemberID=?")) || dbErr($rodb,$res,"cannot create destination (Prepare)");
$stmt->bind_param('ssiis', $data->name,$data->location,$data->duration,$data->duration_instruction,$cuser) || dbErr($rodb,$res,"cannot create destination (bind)");
$stmt->execute() || dbErr($rodb,$res,"cannot create destination");
$rodb->commit();
invalidate('boat');
invalidate('trip');
$rodb->close();
echo json_encode($res);
