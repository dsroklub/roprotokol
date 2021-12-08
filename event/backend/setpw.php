<?php
require("inc/utils.php");
$res=array ("status" => "ok");
$json = file_get_contents("php://input");
$data=json_decode($json);
$cuser=$_SERVER['PHP_AUTH_USER'];
verify_real_user("skifte password");
include("inc/common.php");
require("db.php");
$pw=$data->pw;
assert(strlen($pw)>7);
$memberId=$cuser;
$hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));
$istmt = $rodb->prepare("UPDATE authentication SET password=?,newpassword=? WHERE member_id in (SELECT id from Member where MemberId=?)") or dbErr($rodb,$res,"setpw");
$istmt->bind_param('sss', $hpw,$pw,$memberId) || dbErr($rodb,$res,"pwerr");
$istmt->execute() || dbErr($rodb,$res,"pwerr");
echo json_encode($res);
