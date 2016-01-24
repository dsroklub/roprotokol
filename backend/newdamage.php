<?php
include("inc/common.php");

$data = file_get_contents("php://input");
error_log("new damage");
$newdamage=json_decode($data);

$rodb->query("BEGIN TRANSACTION");
error_log(json_encode($newdamage));
error_log("rep ".json_encode($newdamage->reporter->id));

$mid=-1;
if ( $newdamage->reporter->id) {
    $ms="SELECT id FROM Member WHERE MemberID=?";
        if ($stmt = $rodb->prepare($ms)) {
            $stmt->bind_param('i', $newdamage->reporter->id);
            if ($stmt->execute()) {
                $rid= $stmt->get_result()->fetch_assoc();
                if ($rid) {
                    $mid=$rid["id"];
                }
            } else {
                error_log("member lookup DB error ".$stmt->error);
            }
        } else {
            error_log("new damage database error ".$stmt->error);
        }
}
    
if ($stmt = $rodb->prepare("INSERT INTO Damage(Boat,Degree,ResponsibleMember,Description,Created) VALUES(?,?,?,?,NOW())")) { 
    $stmt->bind_param('iiis', $newdamage->boat->id , $newdamage->degree, $mid, $newdamage->description);
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

$rodb->query("END TRANSACTION");

invalidate('boat');
$rodb->close();
?> 
