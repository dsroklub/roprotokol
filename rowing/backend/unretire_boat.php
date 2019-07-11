<?php
include("inc/common.php");
include("inc/verify_user.php");

$data = file_get_contents("php://input");
$boat=json_decode($data);
$rodb->begin_transaction();
error_log("unretire boat ".json_encode($boat));
$stmt = $rodb->prepare('UPDATE Boat SET Decommissioned=NULL,Location=\'DSR\' WHERE Name=?') or dbErr($rodb,$res,"unretire prep");
$stmt->bind_param('s', $boat->name) or dbErr($rodb,$res,"unretire bind");
error_log("unretire exe $boat->name");
$stmt->execute() or dbErr($rodb,$res,"unretire exe");
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
