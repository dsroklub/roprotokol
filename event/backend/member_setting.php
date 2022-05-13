<?php
include("inc/common.php");
include("inc/utils.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
$res=array ("status" => "ok");
$s="SELECT * FROM member_setting
   WHERE member IN (SELECT id FROM Member WHERE MemberId=?)";

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"member setting");
$stmt->bind_param("s", $cuser) || dbErr($rodb,$res,"member setting bind");
$stmt->execute() || dbErr($rodb,$res,"MEMBER SETTING");
$result=$stmt->get_result() or dbErr($rodb,$res,"Error in stat query: ");
if ($row = $result->fetch_assoc()) {
    echo json_encode($row,JSON_PRETTY_PRINT,JSON_FORCE_OBJECT);
} else {
    echo '{}';
}

invalidate("settings");
