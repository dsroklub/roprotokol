<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$boat=json_decode($data);

$rodb->begin_transaction();
error_log("update_level ".json_encode($boat));

if ($stmt = $rodb->prepare("UPDATE Boat SET level=? WHERE id=?")) {
    $stmt->bind_param('ii', $boat->level,$boat->id);
    $stmt->execute() || $rodb->dump_debug_info();
} else {
    error_log("update level error :".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
