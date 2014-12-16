<?php
include("inc/common.php");

$season=date('Y');

error_log('create trip ss'.$season);
if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
  printf("Error loading character set utf8: %s\n", $rodb->error);
}

$data = file_get_contents("php://input");
$newtrip=json_decode($data);
if ($stmt = $rodb->prepare("INSERT INTO Trip(Season,BoatID,Destination,TripTypeID,CreatedDate,EditDate,OutTime,ExpectedIn) VALUES(?,?,?,?,NOW(),NOW(),?,?)")) { 
     $stmt->bind_param('iisiss', $season, $newtrip->boat->id , $newtrip->destination->name, $newtrip->triptype->id, $newtrip->starttime, $newtrip->expectedtime);
     error_log('now EXE');
     $stmt->execute(); 
} 

 if ($stmt = $rodb->prepare("INSERT INTO TripMember(TripID,Season,Seat,MemberID,MemberName,CreatedDate,EditDate)  VALUES(LAST_INSERT_ID(),?,?,?,?,NOW(),NOW())")) {
     $seat=1;
     foreach ($newtrip->rowers as $rower) {
         $stmt->bind_param('iiis',$season,$seat, $rower->id,$rower->name);
         $stmt->execute();
         $seat+=1;
     }
 } 

$rodb->close();
?> 
