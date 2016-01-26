<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log("set event ".$data->event);

if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) { 
    $stmt->bind_param('s', $data->event);
    $stmt->execute();
} 
$rodb->commit();
$rodb->close();
# invalidate('trip');
echo json_encode($res);
?> 
