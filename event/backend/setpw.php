<?php
require("inc/utils.php");

$res=array ("status" => "ok");
$json = file_get_contents("php://input");
$data=json_decode($json);
$cuser=$_SERVER['PHP_AUTH_USER'];
verify_real_user("skifte password");
include("../../rowing/backend/inc/common.php");
require("db.php");
$pw=$data->pw;
$memberId=$cuser;
$hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));

if ($istmt = $rodb->prepare(
    "UPDATE authentication SET password=?,newpassword=? WHERE member_id in (SELECT id from Member where MemberId=?)")) {
    $istmt->bind_param('sss', $hpw,$pw,$memberId) || error_log($link->error);
    $istmt->execute() || error_log("pw update error: ". $link->error);
} else {
    error_log("Prepare Error:". $link->error);
}
