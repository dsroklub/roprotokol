<?php
require("inc/utils.php");
include("inc/common.php");
require_once("Mail.php");
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newevent=json_decode($data);
$message='';
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
verify_real_user();
// error_log("EVENTCREATE NEWEVENT user=$cuser: ".print_r($newevent,true));
$stmt = $rodb->prepare(
        "INSERT INTO event(owner, boats, start_time, end_time, distance, max_participants, location, name, category, comment,open,auto_administer,destination)
         SELECT Member.id, ?,CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,?,?,?,?,?,?
         FROM Member
         WHERE
           MemberId=?
         ") or dbErr($rodb,$res,"event create prep");

$triptype="NULL";
if (empty($newevent->destination)) {
    $destination=null;
} else {
    $destination=$newevent->destination->name;
}
$stime=date('Y-m-d H:i:s', strtotime($newevent->starttime));
$etime=empty($newevent->endtime)?null:date('Y-m-d H:i:s', strtotime($newevent->endtime));
$eventopen=$newevent->open??0;
$stmt->bind_param(
        'sssiissssiiss',
        $newevent->boat_category->name,
        $stime,
        $etime,
//      $triptype, TRIPTYPE not implemented
        $newevent->distance,
        $newevent->max_participants,
        $newevent->location,
        $newevent->name,
        $newevent->category->name,
        $newevent->comment,
        $eventopen,
        $newevent->automatic,
        $destination,
        $cuser) ||  dbErr($rodb,$res,"create event BIND error ");

$stmt->execute() || dbErr($rodb,$res,"create event EXE ");

$rd=$rodb->query("SELECT LAST_INSERT_ID() as event_id FROM DUAL")->fetch_assoc() or dbErr($rodb,$res,"event create last ID");
$event_id=$rd["event_id"];
error_log(" event_id: $event_id ".print_r($rd,true));

if ($newevent->owner_in) {
    $ostmt = $rodb->prepare(
        "INSERT INTO event_member(member,event,enter_time,role)
         SELECT Member.id, ? ,NOW(),'owner' FROM Member WHERE MemberId=?") or dbErr($rodb,$res,"event create owner prep");
    $ostmt->bind_param('is',$event_id, $cuser) ||  dbErr($rodb,$res,"event create owner bind");
    $ostmt->execute() ||  dbErr($rodb,$res,"event create owner exe");
    error_log("inserted owner $cuser");
}

// members
$toMemberIds=array();
$istmt = $rodb->prepare(
    "INSERT INTO event_invitees(member,event,role)
     SELECT Member.id, LAST_INSERT_ID(),'member' From Member WHERE MemberId=?") or dbErr($rodb,$res,"event invitees prep");
foreach ($newevent->invitees as $invitee) {
    $mid=$invitee->member_id??$invitee->id;
    $toMemberIds[]=$mid;
    $istmt->bind_param('s',$mid) ||  dbErr($rodb,$res,"event invitees bind");
    $istmt->execute() || dbErr($rodb,$res,"event invitees exe");
}

// Now Store message
$subject="Invitation til $newevent->name";
$emailbody="Invitation til https://aftaler.danskestudentersroklub.dk/frontend/event/#!timeline?event=$event_id " . $newevent->comment;
$body="Invitation til https://aftaler.danskestudentersroklub.dk/frontend/event/#!timeline?event=$event_id " . $newevent->comment;
$stmt = $rodb->prepare(
        "INSERT INTO event_message(member_from, event, created, subject, message)
         SELECT mf.id, ?,NOW(),?,?
         FROM Member mf
         WHERE mf.MemberId=?") or dbErr($rodb,$res,"event invitees prep");
$stmt->bind_param(
    'ssss',
    $event_id,
    $subject,
    $body,
    $cuser) || dbErr($rodb,$res,"event message bind");
$stmt->execute() || dbErr($rodb,$res,"event message exe");

$iforas="";
if (!empty($newevent->fora)) {
    $ifors=array();
    $stmt = $rodb->prepare("INSERT INTO event_forum(event,forum) VALUES(?,?)") or dbErr($rodb,$res,"newevent forum");
    foreach ($newevent->fora as $forum) {
        $stmt->bind_param('is',$event_id,$forum->forum) || dbErr($rodb,$res,"newevent forum bind");
        $stmt->execute() || dbErr($rodb,$res,"newevent forum exe");
        $ifora[]="'".$rodb->real_escape_string("$forum->forum")."'";
    }
    $iforas=implode(",",$ifora);
}

if (!empty($newevent->fora)) {
    $stmt = $rodb->prepare(
        "INSERT INTO member_message(member, message)
         SELECT DISTINCT Member.id,LAST_INSERT_ID()
         FROM Member, forum_subscription
         WHERE
             Member.id=forum_subscription.member AND
             forum_subscription.forum IN ($iforas)") or dbErr($rodb,$res,"newevent fora member_message prep");
    $stmt->execute() || dbErr($rodb,$res,"newevent for member message exe");
    invalidate("message");
}

// TODO also email to forum member invitees. NEL

if (count($toMemberIds)>0) {
    $qMarks = str_repeat('?,', count($toMemberIds) - 1) . '?';
    $mi=array();
    $ml=array();
    foreach($toMemberIds as $key => $mr) {
        $ml[$key] = &$toMemberIds[$key];
        $mi[$key] = &$toMemberIds[$key];
    }
    array_unshift($mi, str_repeat('s', count($toMemberIds)));
    array_unshift($ml, str_repeat('s', count($toMemberIds)));
    $qMarks = str_repeat('?,', count($toMemberIds) - 1) . '?';
    $stmt=$rodb->prepare(
        "INSERT INTO member_message(member, message)
         SELECT Member.id, LAST_INSERT_ID()
         FROM Member
         WHERE
              MemberId IN ($qMarks) AND Member.id NOT IN
               (SELECT member from member_message m2 WHERE m2.message=LAST_INSERT_ID())") or dbErr($rodb,$res,"newevent insert member messate prep");
    call_user_func_array( array($stmt, 'bind_param'), $ml);
    $stmt->execute() || dbErr($rodb,$res,"Error in mm INSERT query");
    invalidate("message");
    // Now email
    $mail_headers = array(
        'From'                      => "Roaftaler i Danske Studenters Roklub <aftaler_noreply@danskestudentersroklub.dk>",
        //         'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
        'Content-Transfer-Encoding' => "8bit",
        'Content-Type'              => 'text/plain; charset="utf8"',
        'Date'                      => date('r'),
        'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
        'MIME-Version'              => "1.0",
        'X-Mailer'                  => "PHP-Custom",
        'Subject'                   => "$subject"
    );
    $toEmails=array();
    $stmt=$rodb->prepare(
        "SELECT email FROM Member
         WHERE MemberId IN ($qMarks) AND email IS NOT NULL") or dbErr($rodb,$res,"Error in email query");
    call_user_func_array(array($stmt, 'bind_param'), $mi);
    $stmt->execute() or dbErr($rodb,$res,"Error in location query: exe");
    $result= $stmt->get_result() or dbErr($rodb,$res,"Error in location query res:");
    while ($rower = $result->fetch_assoc()) {
        //         error_log(print_r($rower,true));
        $toEmails[] = $rower['email'];
    }
    $smtp = Mail::factory('sendmail', array ());
    $mail_status = $smtp->send($toEmails, $mail_headers, $emailbody);
    if (PEAR::isError($mail_status)) {
        $res["status"]="warning";
        $res["message"] = "Kunne ikke sende invitationer med email til ".implode(",",$toEmails)." : " . $mail_status->getMessage();
    }
}
invalidate("event");
invalidate("message");
echo json_encode($res);
