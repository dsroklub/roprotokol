<?php
include("../../rowing/backend/inc/common.php");

error_log("delete team ");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);


$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("DELETE FROM instruction_team WHERE name=?")) {
    $stmt->bind_param('s',
    $reg->name
    );
    if (!$stmt->execute()) {
        dbErr(@$rodb,@$res);
    }
    invalidate("gym");
    $rodb->close();
} else {
    dbErr(@$rodb,@$res);
}
echo json_encode($res);
?> 
