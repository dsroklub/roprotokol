<?php
require("inc/utils.php");
include("../../rowing/backend/inc/common.php");

$res=array ("status" => "ok");
verify_real_user("lave nye begivenheder");
$data = file_get_contents("php://input");
$subscription=json_decode($data);
$message='';
$error=null;
error_log(print_r($subscription,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$role="member";
if (!empty($subscription->new_role)) {
    $role=$subscription->new_role;
}

// TODO, check max and use waiting list

if ($stmt = $rodb->prepare("select count('x') as num_members from event_member where event =? and role!='wait' and role!='supplicant'")) {
    $stmt->bind_param("s", $subscription->event_id);
    $stmt->execute();
     $rm= $stmt->get_result() or die("Error in event join prep query: " . mysqli_error($rodb));     
     $realmembers=$rm->fetch_assoc()['num_members'];
} else {
    $error=" event join pre ".mysqli_error($rodb);
}

if ($stmt = $rodb->prepare("select max_participants,boat_category,owner,auto_administer,open from event where id=?")) {
    $stmt->bind_param("s", $subscription->event_id);
    $stmt->execute();
    $result= $stmt->get_result() or die("Error in event info query: " . mysqli_error($rodb));     
    $fa=$result->fetch_assoc();
    $open=$fa['open'];
    $autoadminister=$fa['auto_administer'];
    $max=$fa['max_participants'];
    $owner=$fa['owner'];
} else {
    $error=" event join pre ".mysqli_error($rodb);
}

error_log("event join owner=$owner, open=$open, max=$max, auto=$autoadminister, realm=$realmembers");

if ($max and $open and $max>0 and $max<=$realmembers and $role!="supplicant") {        
    $role="wait";
    error_log("new role $role");
}

$res["role"]=$role;

if ($stmt = $rodb->prepare(
        "INSERT INTO event_member(member,event,enter_time,role)
         SELECT Member.id, event.id,NOW(),IF(event.open>0,?,'supplicant')
         FROM Member,event
         WHERE 
           event.id=? AND
           MemberId=?
         ")) {

    $stmt->bind_param(
        'sss',
        $role,
        $subscription->event_id,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" event join exe ".mysqli_error($rodb);
        $message=$message."\n"."event join insert error: ".mysqli_error($rodb);
    } 
}
if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
echo json_encode($res);
