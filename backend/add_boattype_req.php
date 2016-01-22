<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction(); // 
error_log('add right '.json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO  BoatRights (boat_type,required_right,requirement ) VALUES (?,?,?)")) {
    $stmt->bind_param('iss', $data->boattype->id,$data->right,$data->subject);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
