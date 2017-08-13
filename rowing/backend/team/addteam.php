<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

error_log("add new team ");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);


$res=array ("status" => "ok");

//    $reg->teamkey not used

if ($stmt = $rodb->prepare("INSERT INTO team (name, description, dayofweek, timeofday, teacher) VALUES(?,?,?,?,?)")) {
    $stmt->bind_param('sssss',
    $reg->name,
    $reg->description,
    $reg->dayofweek,
    $reg->timeofday,
    $reg->teacher
    );
    if (!$stmt->execute()) {
        $res["status"]=$rodb->error;
        error_log("OOOP ".$rodb->error);
    }
    invalidate("gym");
    $rodb->close();
} else {
    error_log("OOOP2 ".$rodb->error);
}
echo json_encode($res);
?> 
