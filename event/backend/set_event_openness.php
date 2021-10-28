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
    $stmt = $rodb->prepare(
        "UPDATE event
         SET open=?
         WHERE id=?"
    ) or dbErr($rodb,$res,"set evt open prep");
    $stmt->bind_param(
        'is',
        $event->open,
        $event->event_id
    ) ||  dbErr($rodb,$res,"set event openness BIND ");
    $stmt->execute() || dbErr($rodb,$res,"evt open set exe");
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
