<?php
require("inc/utils.php");

$res=array ("status" => "ok");
$json = file_get_contents("php://input");
$data=json_decode($json);
$cuser=$_SERVER['PHP_AUTH_USER'];
verify_real_user("skifte password");
error_log("data=".print_r($data,true)."XX");
include("../../rowing/backend/inc/common.php");
require("db.php");
$pw=$data->pw;
$memberId=$cuser;
$hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));
error_log("new  pw= $pw $hpw mid=$memberId");

if ($istmt = $rodb->prepare(
    "UPDATE authentication SET password=?,newpassword=? WHERE member_id in (SELECT id from Member where MemberId=?)")) {
                error_log("now Bind");
                $istmt->bind_param('sss', $hpw,$pw,$memberId) || error_log($link->error);
                error_log("now EXE");
                $istmt->execute() || error_log("pw update error: ". $link->error);                            
} else {
    error_log("Prepare Error:". $link->error);
}
