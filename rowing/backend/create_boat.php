<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
error_log("new boat ".json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO Boat (Name,BoatType,Location,Created) VALUES(?,?,?,NOW())")) {
    $stmt->bind_param('sis', $data->name,$data->boat_type,$data->location);
    $stmt->execute();
} else {
    error_log("OOOP".$rodb->error);
}

if ($stmt = $rodb->prepare("SELECT LAST_INSERT_ID() as boat_id FROM DUAL")) {
    $stmt->execute();
    $result= $stmt->get_result() or die("Error get new boat ID query: " . mysqli_error($rodb));
    $res['boat_id']= $result->fetch_assoc()['boat_id'];
} else {
    error_log($rodb->error);
}

$rodb->commit();
$rodb->close();
invalidate('boat');
echo json_encode($res);
