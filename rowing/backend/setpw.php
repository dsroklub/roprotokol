<?php
$res=array ("status" => "ok");
$json = file_get_contents("php://input");
$data=json_decode($json);
$cuser=$_SERVER['PHP_AUTH_USER'];

error_log("data=".print_r($data,true)."XX");

include("../../rowing/backend/inc/common.php");
require("db.php");
$pw=$data->pw;
$memberId=$cuser;
$hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));
error_log("new  pw= $pw $hpw mid=$memberId");

$istmt = $rodb->prepare("UPDATE authentication SET password=?,newpassword=? WHERE member_id in (SELECT id from Member where MemberId=?)") or dbErr($rodb,$res,"setpw");
$pw='XXX';
$istmt->bind_param('sss', $hpw,$pw,$memberId) || dbErr($rodb,$res,"pwerr");
$istmt->execute() || dbErr($rodb,$res,"pwerr");
echo json_encode($res);
