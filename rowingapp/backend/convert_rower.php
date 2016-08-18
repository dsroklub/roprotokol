<?php
include("inc/common.php");
include("inc/verify_user.php");
$res=array ("status" => "ok");

$data = file_get_contents("php://input");
$fromto=json_decode($data);
$error=null;
error_log('convertrower '.json_encode($fromto));

$rodb->begin_transaction();


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
	$stmt = $rodb->prepare($sql);
	if (!$stmt) {
		$error = "Prepare error: " . $rodb->error;
	} else if (! $stmt->bind_param('ss', $fromto->to->id,$fromto->from->id) ) {
		$error = "Bind error: " . $rodb->error;
	} else if (!$stmt->execute()) {
       	$error= "Execute error: " . $rodb->error;
	}
	if ($error) {
		$error .= " <<< $sql >>>";
	    break;
	}
}


if ($error) {
    error_log('convert_rower DB error ' . $error);
    $res['message']='Could not convert rower';
    $res['status']='error';
    $res['error']=$error;
    $rodb->rollback();
} else {
    $rodb->commit();
    invalidate("member");
}

$rodb->close();
echo json_encode($res);
?> 
