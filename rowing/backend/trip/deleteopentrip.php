<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$message="";
error_log('close trip');
$data = file_get_contents("php://input");
$closedtrip=json_decode($data);

$tripinfo="$closedtrip->triptype: $closedtrip->boat til $closedtrip->destination ud $closedtrip->outtime med ". $closedtrip->rowers[0]->name;
$rodb->begin_transaction();
error_log("slet $closedtrip->trip_id $tripinfo");
eventLog("slettet $closedtrip->trip_id $tripinfo");

if ($stmt = $rodb->prepare("DELETE FROM Trip WHERE id=? AND InTime IS NULL")) {
  $stmt->bind_param('i', $closedtrip->trip_id);
  $stmt->execute();
  $result= $stmt->get_result();
}

if ($error) {
 $res['status']='error';
  $res['error']=$error;
}
$res['message']=$message;
$res['boat']=$closedtrip->boat;

$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
