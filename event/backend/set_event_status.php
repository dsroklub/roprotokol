<?php
include("../../rowing/backend/inc/common.php");
require_once("inc/user.php");
include("messagelib.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("SET event status $data\n");
$event=json_decode($data);
$message='set event status ';

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if (check_event_owner($event->event_id)) {
    if ($stmt = $rodb->prepare(
        "UPDATE event
         SET status=?
         WHERE id=?"
    )
    ) {        
        $stmt->bind_param(
            'ss',
            $event->status,
            $event->event_id
        ) ||  die("set event stauts BIND errro ".mysqli_error($rodb));
        
        if ($stmt->execute()) {
            error_log("set evt status set OK " .print_r($event,true));
            $res=post_event_message($event->event_id, $event->name . " " .$event->status,  "ny status for begivenhed $event->name");
        } else {
            $error=" evt status set exe ".mysqli_error($rodb);
            $message=$message."\n"."role update error: ".mysqli_error($rodb);
        }
    } else {
        $error=" event status set ".mysqli_error($rodb);
        error_log($error);
    }
    
    if ($error) {
        error_log($error);
        $res['message']=$message;
        $res['status']='error';
        $res['error']=$error;
    }
} else {
    $error=" ikke ejer af begivenhed ";
    $res["status"]="error";
}
invalidate("event");
error_log(print_r($res,true));
echo json_encode($res);
?> 
