<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$location = $data->location;
$rodb->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

if ($stmt = $rodb->prepare("UPDATE Boat set Location=? Where id=?")) { 
    $stmt->bind_param('si', $data->location,$data->id);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
