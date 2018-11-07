<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$boattype=json_decode($data);

$rodb->begin_transaction();
error_log("boattype update ".json_encode($boattype));

if ($stmt = $rodb->prepare("UPDATE BoatType SET SeatCount = ?, Description = ?, Category = ?, rights_subtype=?, Updated = NOW() WHERE Name=?")) { 
    $stmt->bind_param('isiss', $boattype->seatcount, $boattype->description, $boattype->category, $boattype->rights_subtype,$boattype->name);
    $stmt->execute() ||  error_log("update boat type exe  error:".$rodb->error);
} else {
    error_log("update boat type prepare error :".$rodb->error);
}

$rodb->commit();
$rodb->close();

invalidate('boat');
echo json_encode($res);
