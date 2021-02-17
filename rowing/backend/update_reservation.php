<?php
include("inc/common.php");
include("inc/verify_user.php");
$rv = json_decode(file_get_contents("php://input"));

$stmt=$rodb->prepare("UPDATE reservation SET dayofweek=?,start_time=?,end_time=?
 WHERE id=?") or dbErr($rodb,$res,"res update prepare");
error_log("Reservations for ".$rc->boat." sat til ". print_r($rc,true)." af $cuser");

$stmt->bind_param('issi',$rv->dayofweek,$rv->start_time->timestring,$rv->end_time->timestring,$rv->id) || dbErr($rodb,$res,"SET res conf");
$stmt->execute() || dbErr($rodb,$res,"set res conf");
//eventLog("Reservationskonfiguration for ".$rc->name." sat til ".$rc->boat." af $cuser");
invalidate('reservation');
$rodb->commit();
$rodb->close();
echo json_encode($res);
