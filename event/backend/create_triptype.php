<?php
include("inc/common.php");
require("inc/utils.php");
include("inc/verify_user.php");
verify_right(["admin"=>["roprotokol"]]);

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction();
$stmt = $rodb->prepare("INSERT INTO TripType (Name,Description,Created,Active,tripstat_name) VALUES (?,?,NOW(),1,?)") or dbErr($rodb,$res,"create turtype");
$stmt->bind_param('sss', $data->name,$data->description,$data->name) || dbErr($rodb,$res,"create turtype b");
$stmt->execute() || dbErr($rodb,$res,"create turtype x");
$last = $rodb->query("SELECT LAST_INSERT_ID() AS lastid FROM DUAL") or dbErr($rodb,$res,"lastid");
$res['triptypeid']= $last->fetch_assoc()['lastid'];
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
