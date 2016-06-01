<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$boattype=json_decode($data);

$rodb->begin_transaction();
error_log("boattype update ".json_encode($boattype));

if ($stmt = $rodb->prepare("UPDATE BoatType SET SeatCount = ?, Description = ?, Category = ?, rights_subtype=?, Name = ?, Updated = NOW() WHERE id=?")) { 
    error_log("now exe upd");
    $stmt->bind_param('isissi', $boattype->seatcount, $boattype->description, $boattype->category, $boattype->rights_subtype,$boattype->name, $boattype->id);
    $stmt->execute() ||  error_log("update boattype exe  error:".$rodb->error);
} else {
    error_log("update boattype prepare error :".$rodb->error);
}

$rodb->commit();
$rodb->close();

invalidate('boat');
echo json_encode($res);
?> 



