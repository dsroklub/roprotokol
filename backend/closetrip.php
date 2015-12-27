<?php
include("inc/common.php");


error_log('close trip');
$data = file_get_contents("php://input");
$closedtrip=json_decode($data);

$rodb->query("BEGIN TRANSACTION");

if ($stmt = $rodb->prepare("SELECT 'x' FROM  Trip WHERE BoatID=? AND TripID IS NOT NULL")) { 
  $stmt->bind_param('i', $closedtrip->id);
  $stmt->execute();
  $result= $stmt->get_result();
  if ($result->fetch_assoc()) {
    echo '{"error":"already checked in this trip"}';
    error_log('trip already closed: '. print_r($closedtrip,true);
    $rodb->close();
    exit(0);
  }
} 

if ($stmt = $rodb->prepare(
  "UPDATE Trip SET InTime = NOW() ".
  " WHERE TripID=?") { 
     $stmt->bind_param('i', $closedtrip->id);
     $stmt->execute(); 
}  
$rodb->query("END TRANSACTION");
$rodb->close();
?> 
