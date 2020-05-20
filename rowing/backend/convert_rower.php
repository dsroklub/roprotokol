<?php
include("inc/common.php");
include("inc/verify_user.php");
$res=array ("status" => "ok");

$data = file_get_contents("php://input");
$fromto=json_decode($data);
$error=null;
error_log('convertrower '.json_encode($fromto));

$rodb->begin_transaction();


if (!$fromto->to) {
    dbErr($todb,$res,"to missing");
}
if (!$fromto->from) {
    dbErr($todb,$res,"from missing");
}

$updates = [
   "UPDATE TripMember, Member as fm, Member as tm Set TripMember.member_id = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND TripMember.member_id=fm.id",
   "UPDATE Damage, Member as fm, Member as tm Set Damage.ResponsibleMember = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND Damage.ResponsibleMember=fm.id",
   "UPDATE Damage, Member as fm, Member as tm Set Damage.RepairerMember = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND Damage.RepairerMember=fm.id",
   "UPDATE IGNORE MemberRights as fmr, Member as fm, Member as tm SET fmr.member_id = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND fmr.member_id=fm.id",
   "UPDATE reservation as r, Member as fm, Member as tm Set r.Member = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND r.member=fm.id",
   "UPDATE reservation as r, Member as fm, Member as tm Set r.CancelledBy = tm.id WHERE tm.MemberID=? AND fm.MemberID=? AND r.CancelledBy=fm.id",
   "DELETE FROM Member where MemberID <> ? AND MemberID = ?"
];


foreach ($updates as $sql) {
    $stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"prep convert rower");
    $stmt->bind_param('ss', $fromto->to->id,$fromto->from->id) || dbErr($rodb,$res,"bind, convert rower");
    $stmt->execute() || dbErr($rodb,$res,"convert rower");
}

$rodb->commit();
invalidate("member");

$rodb->close();
echo json_encode($res);
