<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$correction=json_decode($data);
error_log('new correction '. json_encode($correction));


$rodb->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

if ($correction->deleterequest) {
    if ($stmt = $rodb->prepare("INSERT INTO Error_Trip(Trip,DeleteTrip,CreatedDate,Reporter) VALUES(?,1,NOW(),?")) {
        $stmt->bind_param('is', $correction->id,$correction->reporter);
    }
} else {
    
    if ($stmt = $rodb->prepare("INSERT INTO Error_Trip(Trip,BoatID,Destination,TripTypeID,CreatedDate,EditDate,TimeOut,TimeIn,Distance,Reporter) VALUES(?,?,?,?,NOW(),NOW(),CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?)")) {
        $stmt->bind_param('iisissis', $correction->id,$correction->boat->id, $correction->destination->name, $correction->triptype->id, $correction->outtime, $correction->intime,$correction->distance,$correction->reporter);
        error_log('now EXE '. json_encode($correction));
        if (!$stmt->execute()) {
            $error=mysqli_error($rodb);
            $message=$message."\n"."create trip correction insert error";
        }
    } else {
        $error=mysqli_error($rodb);
        error_log("DB error ".$error);            
    }    
    error_log("\n\nnow all rowers ".json_encode($correction->rowers));
    
    if ($stmt = $rodb->prepare("INSERT INTO Error_TripMember(TripID,Seat,member_id,MemberName,CreatedDate,EditDate) ".
    "SELECT LAST_INSERT_ID(),?,Member.id,?,NOW(),NOW() FROM Member Where MemberID=?"
    )) {
        $seat=1;
        error_log("ROWERS");
        foreach ($correction->rowers as $rower) {
            error_log("SEAT".$seat);
            error_log("DO trip correction mb ".$rower->name);
            $stmt->bind_param('isi',$seat,$rower->name,$rower->id);
            $stmt->execute();
            $seat+=1;
        }
    } else {
        error_log("OOOPs2".$rodb->error);
        $error="trim correct member DB error".mysqli_error($rodb);
    }
}

if ($error) {
    error_log('DB error ' . $error);
    $res['message']=$message.'\n'.$error;
    $res['status']='error';
    $res['error']=$error;
    $rodb->rollback();
} else {
    error_log("committing");    
    $rodb->commit();
}

$res['message']=$message;
invalidate("trip");
$rodb->close();
echo json_encode($res);
?> 
