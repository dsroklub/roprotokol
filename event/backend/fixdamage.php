<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
$data = file_get_contents("php://input");
$fix=json_decode($data);
if (empty($cuser)) {
    dbErr($roDb,$res,"unauthorized");
}
$reporter=member_lookup($cuser)["name"];

$rodb->query("BEGIN TRANSACTION");
$stmt = $rodb->prepare("UPDATE Damage, (SELECT id FROM Member WHERE MemberID=?) m  SET Repaired=NOW(),RepairerMember=m.id WHERE Damage.id=?") or dbErr($rodb,$res,"fixdamage");
$stmt->bind_param('si',$cuser,$fix->damage->id) || dbErr($rodb,$res,"fixdamage bind");
$stmt->execute() || dbErr($rodb,$res,"FIXDAMAGE");
eventLog($reporter." klarmeldte skaden: ".$fix->damage->description." på båden ".$fix->damage->boat);
$rodb->query("END TRANSACTION");
$rodb->close();
invalidate("boat");
echo json_encode($res);
