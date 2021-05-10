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
$stmt = $rodb->prepare("UPDATE Damage, (SELECT id FROM Member WHERE MemberID=?) m  SET Repaired=NOW(),RepairerMember=m.id WHERE Damage.id=?") or dbErr($rodb,$res,"fixdamage");
$stmt->bind_param('si',  $reporter,$fix->damage->id) || dbErr($rodb,$res,"fixdamage bind");
$stmt->execute() || dbErr($rodb,$res,"FIXDAMAGE");
eventLog($fix->reporter->name." klarmeldte skaden: ".$fix->damage->description." på båden ".$fix->damage->boat);
$rodb->query("END TRANSACTION");
$rodb->close();
invalidate("boat");
