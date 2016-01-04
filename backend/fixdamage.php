<?php
include("inc/common.php");
include("inc/verify_user.php");

$data = file_get_contents("php://input");
$fix=json_decode($data);

$rodb->query("BEGIN TRANSACTION");

    error_log($data);
if ($stmt = $rodb->prepare("UPDATE Damage, (SELECT id FROM Member WHERE MemberID=?) m  SET Repaired=NOW(),RepairerMember=m.id WHERE Damage.id=?")) { 
    error_log("XX ". $fix->reporter->id . ",". $fix->damage->id);
    $stmt->bind_param('ii',  $fix->reporter->id,$fix->damage->id);
    $stmt->execute(); 
} else {
    error_log("fix damage database error ");
} 

$rodb->query("END TRANSACTION");
$rodb->close();
?> 
