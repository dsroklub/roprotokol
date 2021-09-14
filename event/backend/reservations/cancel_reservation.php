<?php
include("inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"reservation"]);
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
error_log("cancel res ".print_r($data,true));
$rodb->begin_transaction();
$stmt = $rodb->prepare("DELETE FROM reservation  WHERE id=?")  or dbErr($rodb,$res,"deletes reservation (Exe)");
$stmt->bind_param("i", $data->id) or dbErr($rodb,$res,"delete reservation (Bind)");
$stmt->execute()  or dbErr($rodb,$res,"deletes reservation (Exe c)");
$rodb->commit();
$rodb->close();
invalidate("reservation");
echo json_encode($res);
