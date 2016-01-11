<?php
include("inc/common.php");
include("inc/verify_user.php");

$season=date('Y');
$res=array ("status" => "ok");

$data = file_get_contents("php://input");
$newtrip=json_decode($data);
$message="createtrip  ".json_encode($newtrip);
$error=null;

$rodb->query("BEGIN TRANSACTION");
if ($stmt = $rodb->prepare("SELECT 'x' FROM  Trip WHERE BoatID=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $newtrip->boat->id);
  $stmt->execute();
  $result= $stmt->get_result();
  if ($result->fetch_assoc()) {
      $res["status"]="error";
      $error="already on water";
#      $message='create trip failed, already on water: '. json_encode($newtrip,true);
      error_log($error);
  }
}

if (!$error) {
    if ($stmt = $rodb->prepare("INSERT INTO Trip(Season,BoatID,Destination,TripTypeID,CreatedDate,EditDate,OutTime,ExpectedIn) VALUES(?,?,?,?,NOW(),NOW(),?,?)")) { 
        $stmt->bind_param('iisiss', $season, $newtrip->boat->id , $newtrip->destination->name, $newtrip->triptype->id, $newtrip->starttime, $newtrip->expectedtime);
        error_log('now EXE '. json_encode($newtrip));
        if (!$stmt->execute()) {
            $error=mysqli_error($rodb);
            $message=$message."\n"."create trip insert error";
        }
    } 
    
    if ($stmt = $rodb->prepare("INSERT INTO TripMember(TripID,Season,Seat,MemberID,MemberName,CreatedDate,EditDate)  VALUES(LAST_INSERT_ID(),?,?,?,?,NOW(),NOW())")) {
        $seat=1;
        foreach ($newtrip->rowers as $rower) {
            $stmt->bind_param('iiis',$season,$seat, $rower->id,$rower->name);
            $stmt->execute();
            $seat+=1;
        }
        $rodb->query("END TRANSACTION");
    } else {
        $error=mysqli_error($rodb);
    }
}

if ($error) {
    error_log('DB error ' . $error);
}

$res['message']=$message;
$res['error']=$error;
$rodb->query("END TRANSACTION");
invalidate("trip");
$rodb->close();
echo json_encode($res);
?> 
