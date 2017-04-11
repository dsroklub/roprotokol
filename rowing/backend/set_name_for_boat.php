<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();

if ($stmt = $rodb->prepare("UPDATE Boat set Name=? Where id=?")) { 
    $stmt->bind_param('si', $data->name,$data->id);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
