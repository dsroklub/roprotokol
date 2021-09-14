<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol""right"]]);
$sjondata = file_get_contents("php://input");
$right=json_decode($sjondata);
error_log("new right: ".json_encode($right));
$arg=$right->arg??"";
$stmt = $rodb->prepare("INSERT INTO MemberRightType (member_right,description,arg,showname,predicate,active,category,validity) VALUES (?,?,?,?,?,?,?,?)") or dbErr($rodb,$res,"create right P");
$stmt->bind_param('sssssisi', $right->member_right,$right->description,$arg,$right->showname,$right->predicate,$right->active,$right->category,$right->validity) || dbErr($rodb,$res,"create right bind");
$stmt->execute() || dbErr($rodb,$res,"create right exe");
$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
