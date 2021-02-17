<?php
include("inc/common.php");
include("inc/verify_user.php");

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$start_date=isset($data->start_date)?$data->start_date:null;
$end_date=isset($data->end_date)?$data->end_date:null;
$dow=isset($data->dayofweek)?$data->dayofweek:null;
if (!$dow) {
    $dow=0;
}
if ($dow>0) {
  $start_date=null;
  $end_date=null;
}
$rodb->begin_transaction();
$status=$rodb->query("select * from status")->fetch_assoc() or dbErr($rodb,$res,"status res");
global $admin_id;
$stmt = $rodb->prepare(
    "INSERT INTO reservation (boat,start_time,start_date,end_time,end_date,dayofweek,description,triptype,purpose,configuration,created_by)
     VALUES (?,?,?,?,?,?,?,?,?,?,?)") or dbErr($rodb,$res,"make reservation");

$stmt->bind_param('issssisissi', $data->boat_id,$data->start_time->timestring,$start_date,$data->end_time->timestring,$end_date,$dow,$data->description,$data->triptype_id,$data->purpose,$data->configuration->name,$admin_id ) or dbErr($rodb,$res,"make reservation (Exe)");
$stmt->execute() or dbErr($rodb,$res,"make reservation (Exe)");
$last = $rodb->query("SELECT LAST_INSERT_ID() AS lastid FROM DUAL") or dbErr($rodb,$res,"lastid");
$res['reservationid']= $last->fetch_assoc()['lastid'];
$rodb->commit();
invalidate('boat');
invalidate('reservation');

$rodb->close();
echo json_encode($res);
