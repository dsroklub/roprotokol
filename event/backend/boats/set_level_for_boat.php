<?php
include("../inc/common.php");
include("../inc/utils.php");
//$vr=verify_right(["admin"=>["roprotokol","boat"]]);
$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
error_log("$cuser set level ".json_encode($data));
$rodb->begin_transaction();
$stmt = $rodb->prepare("UPDATE Boat set placement_level=? WHERE id=?") or dbErr($rodb,$res,"could not set level");
$stmt->bind_param('ii', $data->placement_level,$data->id) || dbErr($rodb,$res,"could not set level bind");
$stmt->execute()  || dbErr($rodb,$res,"could not set level exe");
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
