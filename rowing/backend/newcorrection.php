<?php
include("inc/common.php");

$error=null;
$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$correction=json_decode($data);
error_log('new correction '. json_encode($correction));

$rodb->begin_transaction();

if ($correction->deleterequest) {
    if ($stmt = $rodb->prepare("INSERT INTO Error_Trip(Trip,ReasonForCorrection,DeleteTrip,CreatedDate,Reporter,Fixed) VALUES(?,?,1,NOW(),?,0)")) {
        $stmt->bind_param('iss', $correction->id,$correction->reason,$correction->reporter);
        $stmt->execute() || error_log(' delete error trip request failed: '.$rodb->error);
    } else {
        error_log("EC Del error: ".$rodb->error);
    }
} else {
    error_log(" times: ".$correction->outtime." , ". $correction->intime);
    if ($stmt = $rodb->prepare(
        "INSERT INTO Error_Trip(Trip,ReasonForCorrection,BoatID,Destination,TripTypeID,CreatedDate,EditDate,TimeOut,TimeIn,Distance,Reporter,Comment,Fixed) 
                VALUES(?,?,?,?,?,NOW(),NOW(),CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,0)")) {
        $stmt->bind_param('isisississ',
        $correction->id,
        $correction->reason,
        $correction->boat->id,
        $correction->destination->name,
        $correction->triptype->id,
        mysdate($correction->outtime),
        mysdate($correction->intime),
        $correction->distance,
        $correction->reporter,
        $correction->comment
        );
        error_log('now EXE '. json_encode($correction));
        if (!$stmt->execute()) {
            $error="new correction exe ERROR: ".$rodb->error;
            $message=$message."\n"."create trip correction insert error";
        }
    } else {
        $error=mysqli_error($rodb);
        error_log("DB prep error: ".$error);            
    }    
    error_log("\n\nnow all rowers ".json_encode($correction->rowers));
    
    if (empty($error)  and $stmt = $rodb->prepare("INSERT INTO Error_TripMember(ErrorTripID,Seat,member_id,MemberName) ".
    "SELECT LAST_INSERT_ID(),?,Member.id,? FROM Member Where MemberID=?"
    )) {
        $seat=1;
        error_log("ROWERS");
        foreach ($correction->rowers as $rower) {
            error_log("SEAT".$seat);
            error_log("DO trip correction mb ".$rower->name . " ID=".$rower->id);
            $stmt->bind_param('iss',$seat,$rower->name,$rower->id);
            $stmt->execute() || error_log(' error trip member set failed: '.$rodb->error);
            $seat+=1;
        }
    } else {
        error_log("OOOPs2".$rodb->error);
        $error="trim correct member DB error: ".mysqli_error($rodb);
    }
}

if ($error) {
    error_log('DB error:: ' . $error);
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
