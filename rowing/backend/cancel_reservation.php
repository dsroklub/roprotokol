<?php
include("inc/common.php");
include("inc/verify_user.php");

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

error_log("cancel res ".print_r($data,true));
$rodb->begin_transaction();
if (empty($data->configuration)) {
  $stmt = $rodb->prepare("DELETE FROM reservation  WHERE boat=? AND start_time=? AND start_date=? AND dayofweek=?")  or dbErr($rodb,$res,"deletes reservation (Exe)");
  $stmt->bind_param("issi", $data->boat_id,$data->start_time,$data->start_date,$data->dayofweek) or dbErr($rodb,$res,"deletes reservation (Bind)");
} else {
  $stmt = $rodb->prepare("DELETE FROM reservation  WHERE boat=? AND start_time=? AND start_date=? AND dayofweek=? AND configuration=?")  or dbErr($rodb,$res,"deletes reservation (Exe,c)");
  $stmt->bind_param("issis", $data->boat_id,$data->start_time,$data->start_date,$data->dayofweek,$data->configuration) or dbErr($rodb,$res,"deletes reservation (Bind,c)");
}
$stmt->execute()  or dbErr($rodb,$res,"deletes reservation (Exe c)");
$rodb->commit();
$rodb->close();
invalidate("reservation");
echo json_encode($res);

