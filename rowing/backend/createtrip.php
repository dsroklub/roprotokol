<?php
include("inc/common.php");

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newtrip=json_decode($data);
$message="createtrip  ".json_encode($newtrip);
$error=null;

error_log($data);

$rodb->begin_transaction();
if ($stmt = $rodb->prepare("SELECT 'x' FROM  Trip WHERE BoatID=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $newtrip->boat->id);
  $stmt->execute();
  $result= $stmt->get_result();
  if ($result->fetch_assoc()) {
      $res["status"]="error";
      $error="already on water";
#      $message='create trip failed, already on water: '. json_encode($newtrip,true);
      error_log($error);
      error_log($data);
  }
}

if (!$error) {
    $starttime=mysdate($newtrip->starttime);
    $expectedtime=mysdate($newtrip->expectedtime);
    
    // error_log('now new trip'. json_encode($newtrip));
    if ($stmt = $rodb->prepare(
        "INSERT INTO Trip(BoatID,Destination,TripTypeID,CreatedDate,EditDate,OutTime,ExpectedIn,Meter,info,Comment) 
                VALUES(?,?,?,NOW(),NOW(),CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?)")) {
        $info="client: ".$newtrip->client_name;
        $stmt->bind_param('isississ',
        $newtrip->boat->id ,
        $newtrip->destination->name,
        $newtrip->triptype->id,
        $starttime,
        $expectedtime,
        $newtrip->distance,
        $info,
        $newtrip->comments);
        if (!$stmt->execute()) {
            $error=mysqli_error($rodb);
            $message=$message."\n"."create trip insert error: ".mysqli_error($rodb);
        }
    } else {
        $error="trip Insert DB STMT  error: ".mysqli_error($rodb);
        error_log($error);
    }
    
    
    if ($stmt = $rodb->prepare(
        "INSERT INTO TripMember(TripID,Seat,member_id,CreatedDate,EditDate) 
         SELECT LAST_INSERT_ID(),?,Member.id,NOW(),NOW() 
           FROM Member 
           WHERE MemberId=?"
    )) {
        // error_log("Create trip insert tripmembers");
        $seat=1;
        foreach ($newtrip->rowers as $rower) {
            $stmt->bind_param('is',$seat,$rower->id);
            $stmt->execute();
            $seat+=1;
        }
    } else {
        error_log("OOOPS 2 :".$rodb->error);
        $error="createtrip Member DB error: ".mysqli_error($rodb);
    }
}

if (isset($newtrip->event)) {
    if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) {
        $ev=$newtrip->triptype->name ." til ". $newtrip->destination->name." i ".$newtrip->boat->name .": ". $newtrip->event;
        $stmt->bind_param('s', $ev);
        $stmt->execute();
    } else {
        error_log("create trip: log failed");
    }     
}

if ($error) {
    error_log('Create Trip DB error ' . $error);
    $res['message']=$message.'\n'.$error;
    $res['status']='error';
    $res['error']=$error;
    $rodb->rollback();
} else {
    $rodb->commit();
}

$res['message']=$message;
invalidate("trip");
$rodb->close();
echo json_encode($res);
?> 
