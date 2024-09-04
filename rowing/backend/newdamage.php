<?php
include("inc/common.php");
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newdamage=json_decode($data);

$rodb->begin_transaction();
//error_log(json_encode($newdamage));
//error_log("rep ".json_encode($newdamage->reporter->id));
if (empty($cuser)) {
    $reporter=$newdamage->reporter->id;
} else {
    $reporter=$cuser;
}
$stmt = $rodb->prepare("INSERT INTO Damage(Boat,Degree,ResponsibleMember,Description,Created) SELECT ?,?,id,?,NOW() From Member WHERE MemberID=?") or dbErr($rodb,$res,"newdamage prep");
$stmt->bind_param('iiss', $newdamage->boat->id , $newdamage->degree, $newdamage->description,$reporter) || dbErr($rodb,$res,"newdamage bind");
$stmt->execute() || dbErr($rodb,$res,"could not create damage, DB error".$stmt->error);
eventLog($newdamage->reporter->name." meldte skaden: ".$newdamage->description. " grad ".$newdamage->degree." på båden ".$newdamage->boat->name);
$result=$rodb->query("SELECT LAST_INSERT_ID() AS id FROM DUAL") or die ("Error in new id query: " . mysqli_error($rodb));
$nid = $result->fetch_assoc();
//error_log("nid ".$nid['id']);
$newdamage->id=$nid['id'];
$newdamage->boat_id=$newdamage->boat->id;
$newdamage->boat=$newdamage->boat->name;
$newdamage->reporter=$newdamage->reporter->name;
$res['damage']=$newdamage;
$rodb->commit();
invalidate('boat');
$rodb->close();
echo json_encode($res);
