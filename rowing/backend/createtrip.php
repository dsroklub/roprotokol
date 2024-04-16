<?php
include("inc/common.php");

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newtrip=json_decode($data);
$tripDescription=$newtrip->triptype->name ." til ". $newtrip->destination->name." i ".$newtrip->boat->name;
$expectedtime=mysdate($newtrip->expectedtime);

$message="createtrip  "; //.json_encode($newtrip);
$error="";
$logevents=[];
// error_log(" Create trip $data");

$rodb->begin_transaction();
$teamName=null;

if (!empty($newtrip->trip_team)) {
    $teamName=$newtrip->trip_team->name;
}

//error_log("COL BOAT=".print_r($newtrip->boat->location,true)."DD");
if ($newtrip->boat->location != "Andre") {
    if ($stmt = $rodb->prepare("SELECT 'x' FROM  Trip WHERE BoatID=? AND InTime IS NULL AND (OutTime<CONVERT_TZ(?,'+00:00','SYSTEM') OR OutTime<=NOW())")) {
        $stmt->bind_param('is', $newtrip->boat->id,$expectedTime);
        $stmt->execute();
        $result= $stmt->get_result();
        if ($result->fetch_assoc()) {
            $res["status"]="error";
            $error="${newtrip->boat->name} already on water";
            error_log($error);
            error_log($data);
            $res['status']='error';
            $res['error']=$error;
            $rodb->rollback();
            $db->close();
            error_log('Create Trip DB error ' . $error);
            $res['message']=$message.'\n'.$error;
            echo json_encode($res);
            exit(0);
        }
    }
}

$countStmt = $rodb->prepare("SELECT 1+count('x') as year_boat_trips FROM Trip WHERE InTime IS NOT NULL AND BoatID=? AND YEAR(OutTime)=YEAR(NOW()) ") or dbErr($rodb,$res,"create trip count trips");
$countStmt->bind_param('i', $newtrip->boat->id) || dbErr($rodb,$res,"create trip cnt");
$countStmt->execute() || dbErr($rodb,$res,"create trip COUNT");
if ($countRow=$countStmt->get_result()->fetch_assoc()) {
    $res['boattrips']=$countRow['year_boat_trips'];
}

foreach ($newtrip->rowers as $rower) {
    if ($stmt = $rodb->prepare("SELECT Boat.name AS boat, Trip.Destination  FROM Trip,TripMember,Member,Boat WHERE Boat.id=Trip.BoatID AND Member.MemberID=? AND Member.id=TripMember.member_id AND TripMember.TripID=Trip.id AND Trip.InTime IS NULL AND Trip.OutTime < NOW()")) {
        $stmt->bind_param('s', $rower->id);
        $stmt->execute();
        $result= $stmt->get_result();
        if ($r=$result->fetch_assoc()) {
            $res["status"]="error";
            $error .= "$rower->name er allerede på vandet i ".$r["boat"] . " til " . $r["Destination"]."\n";
        }
    }
}
if ($error){
    $rodb->rollback();
    $db->close();
    error_log('Create Trip DB rower error ' . $error);
    $res['message']=$message.'\n'.$error;
    $res['error']=$error;
    echo json_encode($res);
    exit(0);
}

$starttime=mysdate($newtrip->starttime);
// error_log('now new trip'. json_encode($newtrip));
$club=$newtrip->foreign_club??null;
$stmt = $rodb->prepare(
    "INSERT INTO Trip(BoatID,Destination,TripTypeID,CreatedDate,EditDate,OutTime,ExpectedIn,Meter,info,Comment,team,club,starting_place)
                VALUES(?,?,?,NOW(),NOW(),CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,?,?,?)") or dbErr($rodb,$res,"trip Insert DB STMT  error");
$info="client: ".$newtrip->client_name;
$stmt->bind_param('isississsss',
                  $newtrip->boat->id ,
                  $newtrip->destination->name,
                  $newtrip->triptype->id,
                  $starttime,
                  $expectedtime,
                  $newtrip->distance,
                  $info,
                  $newtrip->comments,
                  $teamName,
                  $club,
                  $newtrip->destination->location
) || dbErr($rodb,$res,"trip Insert DB bind error");
$stmt->execute() || dbErr($rodb,$res,"create trip insert error");

$stmt = $rodb->prepare("SELECT LAST_INSERT_ID() as tripid FROM DUAL") or dbErr($rodb,$res,"create trip LastID");
$stmt->execute();
$result= $stmt->get_result() or dbErr($rodb,$res,"Trip id Query");
$res['tripid']= $result->fetch_assoc()['tripid'];

$stmt = $rodb->prepare(
    "INSERT INTO TripMember(TripID,Seat,member_id,CreatedDate,EditDate)
         SELECT LAST_INSERT_ID(),?,Member.id,NOW(),NOW()
           FROM Member
           WHERE MemberId=?"
) or dbErr($rodb,$res,"createtrip Member DB");

$seat=1;
foreach ($newtrip->rowers as $rower) {
    $stmt->bind_param('is',$seat,$rower->id);
    $stmt->execute();
    $seat+=1;
}

if (isset($newtrip->event)) {
    $stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())") or dbErr($rodb,$res,"create trip: log failed");
    $ev= "$tripDescription : ". $newtrip->event;
    $stmt->bind_param('s', $ev);
    $stmt->execute();
}

$rodb->commit();

$stmt=$rodb->prepare("SELECT RemoveDate,CONCAT(FirstName,' ',LastName) AS name, MemberID, member_type FROM Member WHERE Member.MemberID=? AND (member_type=1 OR RemoveDate IS NOT NULL)") or dbErr($rodb,$res,"create trip rower prep");

foreach ($newtrip->rowers as $rower) {
    //error_log("check ".$rower->id);
    $stmt->bind_param('s', $rower->id);
    $stmt->execute() ||dbErr($rodb,$res,"create trip addrower");
    $result= $stmt->get_result();
    if ($r=$result->fetch_assoc()) {
        if (!empty($r["RemoveDate"])) {
            eventLog("$tripDescription : roer ".$r["name"] ." udmeldt " . $r["RemoveDate"]);
        }
        if (($r["member_type"]==1)) {
            eventLog("$tripDescription : roer ".$r["name"] ." er passivt medlem");
        }
    }
}
$rodb->commit();

# DSR 55.71472/12.58661

$now=time();
$iswinter=!date("I");
$sunset=date_sunset($now, SUNFUNCS_RET_TIMESTAMP, 55.71472, 12.58661, 90.5833333, 1);
$sunrise=date_sunrise($now, SUNFUNCS_RET_TIMESTAMP, 55.71472, 12.58661, 90.5833333, 1);
// error_log("SUNS winter=$iswinter, sunset=$sunset, ".date('H:i',$sunset));
if ($iswinter) {
    if ($sunset < $now or $now<$sunrise) {
        $res['notification']='det er mørkt og vinter, I skal ikke ro nu';
    } else if ($sunset < strtotime($newtrip->expectedtime)) {
        $res['notification']='Solen går ned klokken ' . date('H:i',$sunset) . '. I skal være i land kl '.date('H:i',$sunset-1800).'. Er du sikker på, at I kan nå at komme ind i tide?';
    }
} else {
    if ($sunset < $now or $now<$sunrise) {
        $res['notification']='det er mørkt, husk en lygte';
    } else if ($sunset < strtotime($newtrip->expectedtime)) {
        $res['notification']='det bliver mørkt klokken ' . date('H:i',$sunset) .' Husk en lygte';
    }
}

$res['message']=$message;
invalidate("trip");
invalidate("stats");
$rodb->close();
echo json_encode($res);
