<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);


$start_date=($data->start_date)?$data->start_date:"";
$end_date=($data->end_date)?$data->end_date:"";

$rodb->begin_transaction();
error_log("new reservation ".json_encode($data));

if ($stmt = $rodb->prepare("INSERT INTO reservation (boat,start_time,start_date,end_time,end_date,dayofweek,description,triptype,purpose)
   VALUES (?,?,?,?,?,?,?,?,?)")) { 
    $stmt->bind_param('issssisis', $data->boat_id,$data->start_time,$start_date,$data->end_time,$end_date,$data->dayofweek,$data->description,$data->triptype_id,$data->purpose );
    error_log("new reservation  EXE");
    $stmt->execute() || error_log("res error ".$rodb->error);
} else {
    error_log($rodb->error);
} 
$rodb->commit();
$rodb->close();
invalidate('boat');
invalidate('reservation');
echo json_encode($res);
?> 
