<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log("new boat ".json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO Boat (Name,BoatType,Location,Created) ".
" SELECT ?,BoatType.id,?,NOW() FROM BoatType WHERE BoatType.Name=?")) {
    $stmt->bind_param('sss', $data->name,$data->location,$data->category);
    $stmt->execute();
} else {
    error_log("OOOP".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
