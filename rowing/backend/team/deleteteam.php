<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

error_log("delete team ");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);


$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("DELETE FROM team WHERE name=? AND dayofweek=? AND timeofday=?")) {
    $stmt->bind_param('sss',
    $reg->name,
    $reg->dayofweek,
    $reg->timeofday
    );
    if (!$stmt->execute()) {
        error_log("OOOP ".$rodb->error);
        $res["status"]=$rodb->error;
    }
    invalidate("gym");
    $rodb->close();
} else {
    error_log("OOOP2 ".$rodb->error);
}
echo json_encode($res);
?> 
