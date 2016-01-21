<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
error_log('add trip right '.json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO  TripRights (trip_type,required_right,requirement ) VALUES (?,?,?)")) {
    $stmt->bind_param('iss', $data->triptype->id,$data->right,$data->subject);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
