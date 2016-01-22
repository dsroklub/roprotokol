<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log('reject correction:  '.json_encode($data));

if ($stmt = $rodb->prepare("DELETE FROM Error_Trip WHERE id=?")) {
    $stmt->bind_param('i', $data->correction->id);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
