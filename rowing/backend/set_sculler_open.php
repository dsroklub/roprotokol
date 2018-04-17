<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$sculler_open = file_get_contents("php://input");

if ($stmt = $rodb->prepare("UPDATE status SET sculler_open=?")) { 
    $stmt->bind_param('i', $sculler_open);
    $stmt->execute();
} 
$rodb->close();
invalidate('status');
echo json_encode($res);
