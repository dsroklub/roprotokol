<?php
include("inc/common.php");

$data = file_get_contents("php://input");
$fix=json_decode($data);
error_log($data);
$rodb->query("BEGIN TRANSACTION");

    error_log($data);
if ($stmt = $rodb->prepare("UPDATE Damage, (SELECT id FROM Member WHERE MemberID=?) m  SET Repaired=NOW(),RepairerMember=m.id WHERE Damage.id=?")) { 
    error_log("XX ". $fix->reporter->id . ",". $fix->damage->id);
    $stmt->bind_param('ii',  $fix->reporter->id,$fix->damage->id);
    $stmt->execute(); 
} else {
    error_log("fix damage database error ");
} 

if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) {

    error_log("des ".$fix->damage->description);
    error_log("bo ".$fix->damage->boat);
    $ev=$fix->reporter->name." klarmeldte skaden: ".$fix->damage->description." på båden ".$fix->damage->boat;
    $stmt->bind_param('s', $ev);
    $stmt->execute();
}     

$rodb->query("END TRANSACTION");
$rodb->close();
invalidate("boat");
?> 
