<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol","bestyrelse"]]);
$error=null;
$res=array ("status" => "ok");
$sculler_open = file_get_contents("php://input");

if ($stmt = $rodb->prepare("UPDATE status SET sculler_open=?")) {
    $stmt->bind_param('i', $sculler_open);
    $stmt->execute();
}
eventLog("Scullerskilt ". ($sculler_open?"åbnet":"lukket")." af $cuser");
$rodb->close();
invalidate('status');
invalidate('admin');
echo json_encode($res);
