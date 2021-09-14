<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=["status" => "ok"];
$jsondata = file_get_contents("php://input");
$data=json_decode($jsondata);

$rodb->begin_transaction();
$stmt = $rodb->prepare("INSERT INTO Boat (Name,boat_type,Location,Created) VALUES(?,?,?,NOW())") or dbErr($rodb,$res,"create boat (Prepare)");
$stmt->bind_param('sss', $data->name,$data->boat_type->name,$data->location) or dbErr($rodb,$res,"create boat (Bind)");
$stmt->execute() or dbErr($rodb,$res,"create boat");

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
