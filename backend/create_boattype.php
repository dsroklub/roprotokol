<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
error_log("new bt ".json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO BoatType (Name,SeatCount, Category,Created) ".
" VALUES (?,?,?,NOW())")) { 
    $stmt->bind_param('sii', $data->name,$data->numseats,$data->category);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
