<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"reservation"]);
$rc = json_decode(file_get_contents("php://input"));
$stmt=$rodb->prepare("UPDATE reservation_configuration SET selected=? WHERE name=?") or dbErr($rodb,$res,"p set res conf");
$sel=$rc->selected;
$rname=$rc->name;
$stmt->bind_param('is',$sel,$rname) || dbErr($rodb,$res,"SET res conf");
$stmt->execute() || dbErr($rodb,$res,"set res conf");
eventLog("Reservationskonfiguration for ".$rc->name." sat til ".$rc->selected." af $cuser");
$rodb->close();
invalidate('admin');
invalidate('reservation');
echo json_encode($res);
