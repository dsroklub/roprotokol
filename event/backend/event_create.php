<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newevent=json_decode($data);
$message='';
error_log(print_r($newevent,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
$cuser="7854"; // FIXME

if ($stmt = $rodb->prepare(
        "INSERT INTO event(owner, boat_category, start_time, end_time, distance, max_participants, location, name, category, comment)
         SELECT Member.id, ?,CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,?,?,? 
         FROM Member
         WHERE 
           MemberId=?
         ")) {

    $triptype="NULL";
    $stmt->bind_param(
        'sssiisssss',
        $newevent->boat_category->id,
        $newevent->starttime,
        $newevent->endtime,
        $newevent->distance,
//      $triptype, TRIPTYPE not implemented
        $newevent->max_participants,
        $newevent->location,
        $newevent->name,
        $newevent->category->name,
        $newevent->comment,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    error_log("NOW EXE");
    if (!$stmt->execute()) {
        $error=" event exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create event insert error: ".mysqli_error($rodb);
    } 
}

if (empty($error) and $newevent->owner_in) {
    if ($ostmt = $rodb->prepare("INSERT INTO event_member(member,event,enter_time,role)
         SELECT Member.id, LAST_INSERT_ID() ,NOW(),'owner' FROM Member WHERE MemberId=?")) {
        $ostmt->bind_param('s',$cuser) ||  $error=mysqli_error($rodb);        
        if ($ostmt->execute()) {
            error_log("INSERTED member");
        } else {
            $error="event Insert DB STMT  error: ".mysqli_error($rodb);
            $message=$message."\n"."create event owner insert error: ".mysqli_error($rodb);
            error_log($error);
        }                
    } else {
        $error="event owner Insert error: ".mysqli_error($rodb);
    }
}


// members


if ($istmt = $rodb->prepare("INSERT INTO event_invitees(member,event)
         SELECT Member.id, LAST_INSERT_ID() From Member WHERE MemberId=?")) {

    foreach ($newevent->invitees as $invitee) {
        error_log("INVI".print_r($invitee,true));
        $istmt->bind_param('s',$invitee->id) ||  $error=mysqli_error($rodb);        
        if ($istmt->execute()) {
            error_log("OK, inserted inviteee");
        } else {
            $error="event invitee Insert DB STMT  error: ". $rodb->error;
            error_log($error);
            $message="$message \n $error";
        }
    }

    
} else {
    $error="event invitee Insert error: ".mysqli_error($rodb);
}

if (!empty($newevent->forum)) {
    $message=$newevent->comment;
    $title="Invitation til $newevent->name";
    send_to_forum($newevent->forum,$message,$title);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
echo json_encode($res);
?> 
