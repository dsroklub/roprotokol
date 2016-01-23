<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$boat=json_decode($data);

$location = $boat->location;
$rodb->begin_transaction();
error_log("boat update usage ".json_encode($boat));

if ($stmt = $rodb->prepare("UPDATE Boat SET boat_usage=? WHERE id=?")) { 
    $stmt->bind_param('ii', $boat->boatusage,$boat->id);
    $stmt->execute() |  errorlog("update usage error :".$rodb->error);
} else {
    errorlog("update usage error :".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
