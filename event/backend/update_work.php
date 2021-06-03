<?php
include("../../rowing/backend/inc/common.php");
require("utils.php");
include("messagelib.php");
$forum = "vedligehold";

//verify_real_user("registrere timer");
$data = file_get_contents("php://input");
$d=json_decode($data);
$now=time();

function parse_time($t) {
    return($t->year."-".$t->month."-".$t->day." ".$t->hour.":".$t->minute);
}

if ($cuser != "baadhal" ) {
    verify_right("admin","vedligehold");
}
if (isset($d->start_time)) {
    $start_time=parse_time($d->start_time);
} else {
    $start_time=date("Y-m-d H:i:s");
}
if (isset($d->end_time->hour)) {
    $end_time=parse_time($d->end_time);
} else {
    $end_time=date("Y-m-d H:i:s");
}
$work="";
if ($cuser=="baadhal"){
    if (strtotime($end_time) > $now)  {
        $end_time=date("Y-m-d H:i:s");
    }
}

if (isset($d->hours) && $cuser != "baadhal") {
    $hours=$d->hours;
} else {
    $hours=(strtotime($end_time)-strtotime($start_time))/3600;
}

if ($cuser=="baadhal"){
    if ($hours>5) {
        $message = "$d->name afkortet til 3 timer\n";
        $message .="$start_time til $end_time = $hours" ;
        $hours=5;
        error_log($message);
        post_forum_message($forum,"$d->name over 5 timer",$message,$from=null,$forumEmail=null,$sticky=false);
    }
}

if ($cuser=="baadhal" && $now-strtotime($start_time)>60) {
    // roErr("man kan ikke ændre starttidspunkt fra bådhallen");

    // $oldworkstmt = $rodb->prepare("SELECT start_time FROM worklog WHERE id=?") or dbErr($rodb,$res,"upd work check sttime");
    // $oldworkstmt->bind_param("s", $d->id) || dbErr($rodb,$res,"ck stt e");
    // $oldworkstmt->execute() ||  dbErr($rodb,$res,"ck st");
    // $oldtime= $oldworkstmt->get_result()->fetch_assoc()["start_time"];
    // if ((strtotime($oldtime)-strtotime($start_time))  > 15*60) {
    //     $message = "$d->name check ind i fortiden fra $oldtime ændret til $start_time";
    //     error_log($message);
    //     post_forum_message($forum,"$d->name check ind i fortid",$message,$from=null,$forumEmail=null,$sticky=false);
    // }
}



$stmt=$rodb->prepare("UPDATE worklog SET start_time=?,end_time=?, work=?, boat=?, hours=?, task=?  WHERE id=?")  or dbErr($rodb,$res,"updatework q");
$res["hours"]=$hours;
$stmt->bind_param("ssssdss", $start_time,$end_time,$d->work, $d->boat, $hours,$d->task,$d->id) || dbErr($rodb,$res,"update work e");
$stmt->execute() or dbErr($rodb,$res,"updwork EXE");
invalidate("work");
echo json_encode($res);
