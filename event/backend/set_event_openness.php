<?php
include("../../rowing/backend/inc/common.php");
require_once("inc/user.php");
include("messagelib.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$event=json_decode($data);
$message='set event openness ';

$error=null;
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if (check_event_owner($event->event_id)) {
    if ($stmt = $rodb->prepare(
        "UPDATE event
         SET open=?
         WHERE id=?"
    )
    ) {        
        $stmt->bind_param(
            'is',
            $event->open,
            $event->event_id
        ) ||  die("set event openness BIND errro ".mysqli_error($rodb));
        
        if ($stmt->execute()) {
            error_log("set evt openness set OK " .print_r($event,true));
        } else {
            $error=" evt open set exe ".$rodb->error;
        }
    } else {
        $error=" event status set ".mysqli_error($rodb);
        error_log($error);
    }    
} else {
    $error=" ikke ejer af begivenhed ";
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}

invalidate("event");
error_log(print_r($res,true));
error_log("return res");
echo json_encode($res);
?> 
