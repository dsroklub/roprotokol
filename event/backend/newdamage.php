<?php
include("inc/common.php");
include("inc/utils.php");
$newdamage=json_decode(file_get_contents("php://input"));
$rodb->begin_transaction();
//error_log("rep ".json_encode($newdamage->reporter->id));
if (empty($cuser)) {
    dbErr($roDb,$res,"unauthorized");
}
$stmt = $rodb->prepare("INSERT INTO Damage(Boat,Degree,ResponsibleMember,Description,Created) SELECT ?,?,id,?,NOW() From Member WHERE MemberID=?") or dbErr($rodb,$res,"newdamage prep");
$stmt->bind_param('iiss', $newdamage->boat->id , $newdamage->degree, $newdamage->description,$cuser) || dbErr($rodb,$res,"newdamage bind");
$stmt->execute() || dbErr($rodb,$res,"could not create damage, DB error".$stmt->error);
$reporter=member_lookup($cuser)["name"];
eventLog($reporter." meldte skaden: ".$newdamage->description. " grad ".$newdamage->degree." på båden ".$newdamage->boat->name);
$result=$rodb->query("SELECT LAST_INSERT_ID() AS id FROM DUAL") or die ("Error in new id query: " . mysqli_error($rodb));
$nid = $result->fetch_assoc();
$newdamage->id=$nid['id'];
$newdamage->boat_id=$newdamage->boat->id;
$newdamage->boat_type=$newdamage->boat->category;
$newdamage->boat=$newdamage->boat->name;
$newdamage->reporter=$reporter;
$newdamage->created=(new DateTime('NOW'))->format('Y-m-d H:i');
$res['damage']=$newdamage;
$rodb->commit();
invalidate('boat');
$rodb->close();
echo json_encode($res);
