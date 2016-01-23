<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$boat=json_decode($data);

$location = $boat->location;
$rodb->begin_transaction();
error_log("boat update brand ".json_encode($boat));

if ($stmt = $rodb->prepare("UPDATE Boat SET brand_usage=? WHERE id=?")) { 
    $stmt->bind_param('si', $boat->brand,$boat->id);
    $stmt->execute() |  errorlog("update usage error :".$rodb->error);
} else {
    errorlog("update usage error :".$rodb->error);
}

if ($stmt = $rodb->prepare("SELECT FROM boat_brand where name=?")) { 
    $stmt->bind_param('si', $boat->brand);
    $result=$stmt->execute() |  errorlog("update brand error :".$rodb->error);
    if (!$result->fetch_assoc()) {
        if ($stmt = $rodb->prepare("INSERT INTO boat_brand(name) VALUES(?)")) { 
            $stmt->bind_param('s', $boat->brand);
            $stmt->execute() |  errorlog("update usage error :".$rodb->error);
        }
    } else {
        errorlog("update usage error :".$rodb->error);
    } 
} else {
    errorlog("update brand ERROR :".$rodb->error);
}



$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
?> 
