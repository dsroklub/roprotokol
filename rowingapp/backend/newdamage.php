<?php
include("inc/common.php");
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("new damage");
$newdamage=json_decode($data);

$rodb->query("BEGIN TRANSACTION");
error_log(json_encode($newdamage));
error_log("rep ".json_encode($newdamage->reporter->id));

    
if ($stmt = $rodb->prepare("INSERT INTO Damage(Boat,Degree,ResponsibleMember,Description,Created) 
SELECT ?,?,id,?,NOW() From Member WHERE MemberID=?")) { 
    $stmt->bind_param('iisi', $newdamage->boat->id , $newdamage->degree, $newdamage->description,$newdamage->reporter->id);
    if (!$stmt->execute()) {
        error_log("could not create damage, DB error".$stmt->error);
    } 
} else {
    error_log("new damage database error".$stmt->error);
}

if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) {
    error_log("des ".$newdamage->description);
    error_log("bo ".$newdamage->boat->name);
    $ev=$newdamage->reporter->name." meldte skaden: ".$newdamage->description. " grad ".$newdamage->degree." på båden ".$newdamage->boat->name;
    $stmt->bind_param('s', $ev);
    $stmt->execute();
}     
$result=$rodb->query("SELECT LAST_INSERT_ID() AS id FROM DUAL") or die ("Error in new id query: " . mysqli_error($rodb));
$nid = $result->fetch_assoc();
error_log("nid ".$nid['id']);
$newdamage->id=$nid['id'];
$newdamage->boat_id=$newdamage->boat->id;
$newdamage->boat=$newdamage->boat->name;
$newdamage->reporter=$newdamage->reporter->name;

$res['damage']=$newdamage;
$rodb->query("END TRANSACTION");

invalidate('boat');
$rodb->close();
echo json_encode($res);
?> 
