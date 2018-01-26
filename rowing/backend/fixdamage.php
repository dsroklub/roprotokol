<?php
include("inc/common.php");
$data = file_get_contents("php://input");
$fix=json_decode($data);
if (empty($cuser)) {
    $reporter=$fix->reporter->id;
} else {
    $reporter=$cuser;
}

$rodb->query("BEGIN TRANSACTION");
if ($stmt = $rodb->prepare("UPDATE Damage, (SELECT id FROM Member WHERE MemberID=?) m  SET Repaired=NOW(),RepairerMember=m.id WHERE Damage.id=?")) { 
    $stmt->bind_param('si',  $reporter,$fix->damage->id);
    $stmt->execute() || error_log("damage fix DB error: $rodb->error"); 
} else {
    error_log("fix damage database error ");
} 

if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) {
    $ev=$fix->reporter->name." klarmeldte skaden: ".$fix->damage->description." på båden ".$fix->damage->boat;
    $stmt->bind_param('s', $ev);
    $stmt->execute();
}     

$rodb->query("END TRANSACTION");
$rodb->close();
invalidate("boat");
?> 
