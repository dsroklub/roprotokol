<?php
include("../../rowing/backend/inc/common.php");
require_once("inc/user.php");
include("messagelib.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("SET event status $data\n");
$forum=json_decode($data);
$message='toggle forum visibility ';

$error=null;
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if (check_forum_owner($forum)) {
    if ($stmt = $rodb->prepare(
        "UPDATE forum
         SET is_public= NOT is_public
         WHERE forum.name=?"
    )
    ) {        
        $stmt->bind_param(
            's',
            $forum->forum
        ) ||  die("toggle forum visibility " . mysqli_error($rodb));
        
        if ($stmt->execute()) {
            error_log("toggle forum visibility " .print_r($forum,true));
        } else {
            $error=" forum visibility status set exe ".mysqli_error($rodb);
            $message=$message."\n"."forum visibility toggle error: ".mysqli_error($rodb);
        }
    } else {
        $error=" forum visibility set ".mysqli_error($rodb);
        error_log($error);
    }    
} else {
    $error=" $cuser ikke ejer af forum $forum->forum ";
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}

invalidate("forum");
echo json_encode($res);
?> 
