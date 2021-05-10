<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/utils.php");

$error=null;
$res=array ("status" => "error");
$data = file_get_contents("php://input");
$reuse=json_decode(json_decode($data)->reusetrip);

$rodb->begin_transaction();
$s="SELECT JSON_MERGE(
    JSON_OBJECT(
    'id',Trip.id,
    'status','OK',
    'message','',
    'triptype_id',TripType.id,
    'triptype',  TripType.Name,
    'boat_id',Boat.id,
    'boat',Boat.Name,
    'destination', Trip.Destination,
    'intime', Trip.InTime,
    'outtime', DATE_FORMAT(CONVERT_TZ(Trip.OutTime,    'SYSTEM', '+0:00'), '%Y-%m-%dT%T.000Z'),
    'expectedintime', DATE_FORMAT(CONVERT_TZ(Trip.ExpectedIn, 'SYSTEM', '+0:00'), '%Y-%m-%dT%T.000Z'),
    'comment',  MAX(Trip.Comment)),
   CONCAT(
     '{', JSON_QUOTE('rowers'),': [',
     GROUP_CONCAT(JSON_OBJECT(
       'member_id',Member.MemberID,
       'name', CONCAT(Member.FirstName,' ',Member.LastName))),
   ']}')
   ) AS json

   FROM Trip, Boat, TripType, TripMember LEFT JOIN Member ON Member.id = TripMember.member_id
   WHERE Boat.id=Trip.BoatID
     AND Trip.id=?
     AND Trip.InTime IS NULL
     AND TripType.id=Trip.TripTypeID
     AND TripMember.TripID=Trip.id
   GROUP BY Trip.id,TripType.id,Boat.id
   ORDER BY Trip.id ";

$stmt = $rodb->prepare("$s") or dbErr($rodb,$res,"reuse trip");
$stmt->bind_param('i', $reuse->trip_id) || dbErr($rodb,$res,"reuse trip bind");
$stmt->execute() || dbErr($rodb,$res,"reuse trip exe");
$result= $stmt->get_result() or dbErr($rodb,$res,"reuse trip res");
if ($row = $result->fetch_assoc()) {
    $okres=$row["json"];
    error_log("reuse open trip json=". $row["json"]);
} else {
    $error = 'No such trip found';
}

if ($error) {
    error_log("reuse Error: $error :" .$reuse->trip_id);
  // Skip
} elseif ($stmt = $rodb->prepare("DELETE FROM Trip WHERE id=? AND InTime IS NULL")) {
  $stmt->bind_param('i', $reuse->trip_id);
  $stmt->execute();
} else {
  $error = "Could not delete trip";
}

if ($error) {
 http_response_code(500);
 $res['status']='error';
 $res['error']=$error;
 json_encode($res,JSON_PRETTY_PRINT);
 exit(-1);
}
$rodb->commit();
$rodb->close();
invalidate('trip');
echo $okres;
