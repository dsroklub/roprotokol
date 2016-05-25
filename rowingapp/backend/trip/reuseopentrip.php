<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/utils.php");

$error=null;
$res=array ("status" => "ok");
$message="";
error_log('close trip');
$data = file_get_contents("php://input");
$reuse=json_decode($data);

$rodb->begin_transaction();
error_log("reuse open trip ". $reuse->reusetrip);

$s="SELECT Trip.id, TripType.id as triptype_id, TripType.Name AS triptype, Boat.id as boat_id, Boat.Name AS boat, Trip.Destination as destination, Trip.InTime as intime,Trip.OutTime as outtime, Trip.ExpectedIn as expectedintime,GROUP_CONCAT(Member.MemberID,':§§:', MemberName SEPARATOR '££') AS rowers, MAX(Trip.Comment) as comment
   FROM Trip, Boat, TripType, TripMember LEFT JOIN Member ON Member.id = TripMember.member_id  
   WHERE Boat.id=Trip.BoatID AND Trip.id=? AND Trip.InTime IS NULL AND TripType.id=Trip.TripTypeID AND TripMember.TripID=Trip.id
   ORDER BY TripMember.Seat ";

if ($stmt = $rodb->prepare("$s")) { 
  $stmt->bind_param('i', $reuse->reusetrip);
  $stmt->execute();
  $result= $stmt->get_result() or die("Error in reuse query: " . mysqli_error($rodb));
  if ($row = $result->fetch_assoc()) {
      $row['rowers']=multifield_array($row['rowers'],"member_id","name");
      $res['reuse']=$row;
  }
} 

if ($stmt = $rodb->prepare("DELETE FROM Trip WHERE id=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $reuse->reusetrip);
  $stmt->execute();
  $result= $stmt->get_result();
}

if ($stmt = $rodb->prepare("DELETE FROM TripMember WHERE TripID=?")) { 
  $stmt->bind_param('i', $reuse->reusetrip);
  $stmt->execute();
  $result= $stmt->get_result();
} 


if ($error) {
 $res['status']='error';
 $res['error']=$error;
}
$res['message']=$message;

$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res,JSON_PRETTY_PRINT);
?> 
