<?php
include("../../rowing/backend/inc/common.php");
require("utils.php");
verify_right("admin","vedligehold");
$data = file_get_contents("php://input");
$d=json_decode($data);
$stmt=$rodb->prepare("INSERT INTO worker(member_id,assigner, created,created_by,requirement,workertype )
  SELECT m.id,'vedligehold',NOW(),mby.id,?,? FROM Member m, Member mby WHERE m.MemberId=? AND mby.MemberId=?") or dbErr($rodb,$res,"set work");
$stmt->bind_param("dsss", $d->requirement,$d->workertype,$d->id,$cuser) || dbErr($rodb,$res,"create worker");
$stmt->execute() or dbErr($rodb,$res,"create work exe");
invalidate("work");
echo json_encode($res);
