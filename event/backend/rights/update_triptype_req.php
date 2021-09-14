<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"trip","admin"=>"right"]);
$res=array ("status" => "ok");
$tr = json_decode(file_get_contents("php://input"));
$rodb->begin_transaction();
$stmt = $rodb->prepare("UPDATE TripRights SET requirement=? WHERE trip_type=? AND required_right=?") or dbErr($rodb,$res,"update trip req");
$stmt->bind_param('sis', $tr->req->requirement,$tr->triptype->id,$tr->req->required_right) || dbErr($rodb,$res,"update trip req b");
$stmt->execute() || dbErr($rodb,$res,"update trip req X");
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
