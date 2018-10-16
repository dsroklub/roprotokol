<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/utils.php");

$error=null;
$res=array ("status" => "error");
$data = file_get_contents("php://input");
$reuse=json_decode($data);

$rodb->begin_transaction();
error_log("reuse open trip ". $reuse->reusetrip);

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

if ($stmt = $rodb->prepare("$s")) { 
  $stmt->bind_param('i', $reuse->reusetrip);
  $stmt->execute();
  $result= $stmt->get_result();
  if ($result) {
  	if ($row = $result->fetch_assoc()) {
      $okres=$row["json"];
      error_log("reuse open trip json=". $row["json"]);
    } else {
    	$error = 'No such trip found';
    }
  } else {
  	$error = "Error in reuse query: " . mysqli_error($rodb);
  }
} else {
  $error = "Error in reuse query SQL: " . mysqli_error($rodb);
}


if ($error) {
  // Skip
} elseif ($stmt = $rodb->prepare("DELETE FROM Trip WHERE id=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $reuse->reusetrip);
  $stmt->execute();
  $result= $stmt->get_result();
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
