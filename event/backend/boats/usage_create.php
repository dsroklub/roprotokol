<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"boat"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$usage=json_decode($data);
error_log("new usage ".$data);
$newusage=array("name"=>$usage->name,"description"=>$usage->description);
$rodb->begin_transaction();
if ($stmt = $rodb->prepare("INSERT INTO boat_usage(name,Description) VALUES (?,?)")) {
    $stmt->bind_param('ss', $usage->name,$usage->description);
    $stmt->execute() |  error_log("usage create exe error :".$rodb->error);

    $result=$rodb->query("SELECT LAST_INSERT_ID() AS id FROM DUAL") or die("Error in new id query: " . mysqli_error($rodb));
    $nid = $result->fetch_assoc();
    if ($nid) {
        $newid=$nid["id"];
        $newusage["id"]=$newid;
        $res["newusage"]=$newusage;
    }
} else {
    error_log("usage create nm error :".$rodb->error);
}
$rodb->commit();
$rodb->close();
invalidate("boat");
echo json_encode($res);
