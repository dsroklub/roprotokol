<?php
include("inc/common.php");
require("inc/utils.php");
verify_right(["admin"=>["vedligehold"]]);
error_log("rmwork cuser $cuser");
$data = file_get_contents("php://input");
$d=json_decode($data);
$stmt=$rodb->prepare("DELETE FROM worklog WHERE id=? AND season=$workyear") or dbErr($rodb,$res,"rmwork prep");
$stmt->bind_param("s", $d->id) || dbErr($rodb,$res,"rmwork e");
$stmt->execute() or dbErr($rodb,$res,"rmwork exe");
invalidate("work");
echo json_encode($res);
