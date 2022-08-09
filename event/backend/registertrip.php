<?php
include("inc/common.php");

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newtrip=json_decode($data);
$tripDescription=$newtrip->triptype->name ." til ". $newtrip->destination->name." i ".$newtrip->boat->name;

$message="createtrip  "; //.json_encode($newtrip);
$logevents=[];
// error_log(" Create trip $data");

$rodb->begin_transaction();

$teamName=null;

$starttime=mysdate($newtrip->starttime);
    // error_log('now new trip'. json_encode($newtrip));
$club=$newtrip->foreign_club??null;
$stmt = $rodb->prepare(
        "INSERT INTO Trip(BoatID,Destination,TripTypeID,CreatedDate,EditDate,OutTime,InTime,Meter,info,Comment,team,club,starting_place,created_by)
                VALUES(?,?,?,NOW(),NOW(),CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,?,?,?,?)"
) or dbErr($rodb,$res,"register trip prep err");

$starttime=mysdate($newtrip->starttime);
$intime=mysdate($newtrip->intime);
$stmt->bind_param('isississsssi',
                  $newtrip->boat->id ,
                  $newtrip->destination->name,
                  $newtrip->triptype->id,
                  $starttime,
                  $intime,
                  $newtrip->distance,
                  $info,
                  $newtrip->comments,
                  $teamName,
                  $club,
                  $newtrip->destination->location,
                  $cuser
) || dbErr($rodb,$res,"register trip prep err");

$stmt->execute() || dbErr($rodb,$res,"register trip exe");

$stmt = $rodb->prepare("SELECT LAST_INSERT_ID() as tripid FROM DUAL") or dbErr($rodb,$res,"register trip ID prep");
$stmt->execute() || dbErr($rodb,$res,"register trip ID exe");
$result= $stmt->get_result() or dbErr($rodb,$res,"register trip ID res");
$res['tripid']= $result->fetch_assoc()['tripid'];

$stmt = $rodb->prepare(
        "INSERT INTO TripMember(TripID,Seat,member_id,CreatedDate,EditDate)
         SELECT LAST_INSERT_ID(),?,Member.id,NOW(),NOW()
           FROM Member
           WHERE MemberId=?"
    ) or dbErr($rodb,$res,"register tripmember prep");

$seat=1;
foreach ($newtrip->rowers as $rower) {
    $stmt->bind_param('is',$seat,$rower->id) || dbErr($rodb,$res,"register tripmember bind");
    $stmt->execute() || dbErr($rodb,$res,"register tripmember exe");
    $seat+=1;
}
if (isset($newtrip->event)) {
    $stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())") or dbErr($rodb,$res,"register trip log prep");
    $ev= "$tripDescription : ". $newtrip->event;
    $stmt->bind_param('s', $ev) || dbErr($rodb,$res,"register trip log bind");
    $stmt->execute() || dbErr($rodb,$res,"register trip log exe");
}
$rodb->commit();


$now=time();

$res['message']=$message;
invalidate("trip");
invalidate("stats");
$rodb->close();
echo json_encode($res);
