<?php
include("../../rowing/backend/inc/common.php");


$res=array ("status" => "ok", "dirty" => null, "promoted"=>null);
$data = file_get_contents("php://input");
$subscription=json_decode($data);
$message='';
error_log(print_r($subscription,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

error_log("I am $cuser");

function boat_configurations($rowers,$coxs,$max=99999) {
    $nr=$rowers;
    if ($nr>$max) $nr=$max;
    
    $max2=floor($nr/3);
    $max4=floor($nr/5);
    if ($max4>$coxs) {
        $max4=$coxs;
    }
    if ($max2>$coxs) {
        $max2=$coxs;
    }
         
    $mc=0;
    for ($c2=0; $c2<=$max2; $c2++) {
        for ($c4=0; $c4<=$max4; $c4++) {
            if ($c2*3+$c4*5 <= $nr and $c2*3+$c4*5>$mc && $c2+$c4<=$coxs) {
                $mc=$c2*3+$c4*5;
            } 
        }
    }
         
    $bcms=[];
    for ($c2=0; $c2<=$max2; $c2++) {
        for ($c4=0; $c4<=$max4; $c4++) {
            if ($c2*3+$c4*5==$mc && $c2+$c4<=$coxs) {
                $bcms[]=array("i2" => $c2,"i4"=>$c4);
            } 
        }
    }
    error_log("boatcons " .print_r($bcms,true));
    return array("configurations" => $bcms, "on_water"=>$mc,"rowers"=>$nr, "left_out"=>$nr-$mc);    
}


if ($stmt = $rodb->prepare(
        "DELETE FROM event_member
         WHERE 
         event=? AND member IN
         (SELECT Member.id FROM Member WHERE MemberId=?)")) {

    $stmt->bind_param(
        'ss',
        $subscription->event_id,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    if ($stmt->execute()) {
        $stmt = $rodb->prepare(
            "SELECT COUNT(DISTINCT event_member.member) as current, COUNT(DISTINCT em.member) as coxes, max_participants,boats,owner,auto_administer,open FROM event 
                 LEFT JOIN event_member ON event_member.event=event.id AND (event_member.role='member' OR event_member.role='owner') 
                 LEFT JOIN (event_member em JOIN MemberRights on MemberRights.member_id=em.member AND MemberRights.MemberRight='cox') ON em.event=event.id AND (em.role='member' OR em.role='owner') 
                 WHERE  event.id=? GROUP BY event.id");
        $stmt->bind_param("s", $subscription->event_id);
        $stmt->execute();
        $result= $stmt->get_result() or die("Error in event leave query: " . mysqli_error($rodb));     
        $fa=$result->fetch_assoc();
        $auto=$fa['auto_administer'];
        $boats=$fa['boats'];
        $current=$fa['current'];
        $coxes=$fa['coxes'];
        $max=$fa['max_participants'];


        error_log("LEAVE  auto=$auto, boats=$boats, current=$current, cx=$coxes, max=$max" );

        
        if ($auto) {
            // promote wait list.
                        
            $stmt = $rodb->prepare("
    SELECT argument,MemberRight,member,role,enter_time,event_member.member, Member.MemberId as member_id
    FROM event_member,Member LEFT JOIN MemberRights ON member_id=Member.id AND MemberRight='cox' 
    WHERE Member.id=event_member.member AND event=? AND role <> 'member' AND role <> 'owner' ORDER BY enter_time");
            $stmt->bind_param("i", $subscription->event_id);
            $stmt->execute();
            $nonmembers = $stmt->get_result() or die("Error in event leave query: " . mysqli_error($rodb));
            while ($nonmember = $nonmembers->fetch_assoc()) {
                if ($nonmember["role"] == "wait") {
                    if ($nonmember["MemberRight"]!="cox" and $boats=="Inrigggere") {
                        if (count(boat_configurations($current+1,$coxes,$max)["configurations"])<1) {
                            continue;
                        }
                    }
                    $stmt = $rodb->prepare("UPDATE event_member SET role='member' WHERE event=? and member=?");
                    $stmt->bind_param("ii", $subscription->event_id, $nonmember["member"]);
                    $stmt->execute();
                    $res["dirty"]=1;
                    $res["promoted"]=$nonmember["member_id"];
                    break; // FIXME hand 3/5 cascades
                }
            }
        }
        
    } else {
        $error=" event leave exe ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."event join insert error: ".mysqli_error($rodb);
    } 
} else {
    $error=" event leave st ".mysqli_error($rodb);
    error_log($error);
}
if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}

$rodb->commit();
invalidate("event");
echo json_encode($res);
