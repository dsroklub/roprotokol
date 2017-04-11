<?php
include("../../rowing/backend/inc/common.php");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);
$cuser=$_SERVER['PHP_AUTH_USER'];

$res=array ("status" => "ok");
$cuser=$_SERVER['PHP_AUTH_USER'];

if ($reg->pass) {
    $q="INSERT INTO course_requirement_pass (requirement,member_id,passed) SELECT ?,id,NOW() From Member Where MemberId=?";
} else {
    $q="DELETE FROM course_requirement_pass WHERE requirement=? and member_id IN (SELECT id FROM Member where MemberId=?)";
}

if ($stmt = $rodb->prepare($q)) {
    $stmt->bind_param('ss',
    $reg->requirement,
    $reg->aspirant
    );
    if (! $stmt->execute()) {
        dbErr($rodb,$res);
    }
    invalidate("cox");
} else {
     dbErr($rodb,$res);
}


$lq="INSERT INTO cox_log (timestamp, member_id,action,entry) SELECT NOW(),?,?,CONCAT(?,' for ',id) From Member Where MemberId=?";

$action=$reg->pass?"pass":"fail";
if ($stmt = $rodb->prepare($lq)) {
    $stmt->bind_param('ssss',
    $cuser,
    $action,
    $reg->requirement,
    $reg->aspirant
    );
    if (! $stmt->execute()) {
        dbErr($rodb,$res);
    }
    invalidate("cox");
    $rodb->close();
} else {
     dbErr($rodb,$res);
}

echo json_encode($res);
?> 
