<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol","trip"]]);
$data = file_get_contents("php://input");
$data=json_decode($data);
$rodb->begin_transaction();
$stmt = $rodb->prepare("UPDATE TripType SET Active=? WHERE id=?") or dbErr($rodb,$res,"act triptype $data->active");
$stmt->bind_param('ii', $data->active,$data->id);
$stmt->execute() || dbErr($rodb,$res,"ACT triptype $data->active");;
$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
