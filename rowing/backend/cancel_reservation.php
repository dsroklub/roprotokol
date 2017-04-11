<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log("cancel reservation ".json_encode($data));

if ($stmt = $rodb->prepare("DELETE FROM reservation
   WHERE boat=? AND start_time=? AND start_date=? AND dayofweek=?")) { 
    $stmt->bind_param("issi", $data->boat_id,$data->start_time,$data->start_date,$data->dayofweek);
    $stmt->execute() || $rodb->dump_debug_info();
} else {
    error_log("cancel error");
    $res=array ("status" => "db error");
    $rodb->dump_debug_info();
} 
$rodb->commit();
$rodb->close();
invalidate("reservation");
echo json_encode($res);
?> 
