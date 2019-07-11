<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$configuration = file_get_contents("php://input");
if ($stmt = $rodb->prepare("UPDATE status SET reservation_configuration=?")) {
    $stmt->bind_param('s', $configuration);
    $stmt->execute();
}
eventLog("Reservationskonfiguration sat til $configuration af $cuser");
$rodb->close();
invalidate('status');
invalidate('admin');
invalidate('reservation');
echo json_encode($res);
