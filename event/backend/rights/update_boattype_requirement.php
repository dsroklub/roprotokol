<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"trip","admin"=>"right"]);
$res=["status" => "ok"];
$br = json_decode(file_get_contents("php://input"));
$rodb->begin_transaction();
$stmt = $rodb->prepare("UPDATE BoatRights SET requirement=? WHERE boat_type=? AND required_right=?") or dbErr($rodb,$res,"update boat req");
$stmt->bind_param('sss', $br->req->requirement,$br->boattype->name,$br->req->required_right) || dbErr($rodb,$res,"update boat req b");
$stmt->execute() || dbErr($rodb,$res,"update boat req X");
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
