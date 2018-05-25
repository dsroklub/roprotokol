<?php
include("inc/common.php");
include("inc/verify_user.php");

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);

$rodb->begin_transaction();
//error_log('approve correction:  '.json_encode($data));

if ($data->correction->DeleteTrip) {
    error_log('Delete Trip: '.$data->trip);
    if ($stmt = $rodb->prepare("DELETE FROM Trip WHERE id=?")) {
        $stmt->bind_param('i', $data->trip);
        $stmt->execute() || error_log(' DELETE Trip corr failed'.$rodb->error);
    } else {
        error_log('OOOP TRIP delete correction'.$rodb->error);
        $rodb->rollback();
        exit(1);
    }
} else {
    if ($stmt = $rodb->prepare("UPDATE Trip,Error_Trip SET Trip.BoatID=Error_Trip.BoatID, Trip.InTime=Error_Trip.TimeIn, Trip.OutTime=Error_Trip.TimeOut,Trip.Destination=Error_Trip.Destination,Trip.Meter=Error_Trip.Distance,Trip.TripTypeID=Error_Trip.TripTypeID,Trip.Comment=Error_Trip.Comment 
                                WHERE Error_Trip.id=? AND Trip.id=Error_Trip.Trip")) {
        $stmt->bind_param('i', $data->correction->id);
        $stmt->execute() || error_log(' UPDATE exe failed'.$rodb->error);
    } else {
        error_log('OOOP UPDATE'.$rodb->error);
        $rodb->rollback();
        exit(2);
    }
    
    if ($stmt = $rodb->prepare("DELETE From TripMember WHERE TripID=?")) {
        $stmt->bind_param('i', $data->correction->Trip);
        $stmt->execute() || error_log(' DELETE exe failed'.$rodb->error);
    } else {
        error_log('OOOP delete org tripmembers'.$rodb->error);
        $rodb->rollback();
        exit (3);
    }

    if ($stmt = $rodb->prepare("INSERT INTO TripMember (TripID,Seat,member_id,EditDate) SELECT ?,Seat,member_id,NOW() From Error_TripMember WHERE ErrorTripID=?")) {
        $stmt->bind_param('ii', $data->trip,$data->correction->id);
        $stmt->execute() || error_log(' INSERT copy rowers failed'.$rodb->error);
    } else {
        error_log('OOOP prepare insert copy rowers'.$rodb->error);
        $rodb->rollback();
        exit(4);
    }
}

if ($stmt = $rodb->prepare("UPDATE Error_Trip SET Fixed=1 WHERE id=?")) {
    $stmt->bind_param('i', $data->correction->id);
    $stmt->execute() || error_log(' Error trip fix '.$rodb->error);
} else {
    error_log('OOOP update error fixed correction'.$rodb->error);
}    

$rodb->commit();
$rodb->close();
invalidate('trip');
echo json_encode($res);
?> 
