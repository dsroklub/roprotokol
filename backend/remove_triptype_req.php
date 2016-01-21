<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$rodb->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
error_log('remove right '.json_encode($data));

if ($stmt = $rodb->prepare("DELETE FROM TripRights WHERE trip_type=? AND required_right=?")) {
    $stmt->bind_param('is', $data->triptype->id,$data->right);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
