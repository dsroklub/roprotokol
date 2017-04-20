<?php
include("../../rowing/backend/inc/common.php");
include("inc/forummail.php");
require_once("/usr/share/php/Mail.php");


$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$newevent=json_decode($data);
$message='';
error_log(print_r($newevent,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
// $cuser="7854"; // FIXME

if ($stmt = $rodb->prepare(
        "INSERT INTO event(owner, boat_category, start_time, end_time, distance, max_participants, location, name, category, comment)
         SELECT Member.id, ?,CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,?,?,? 
         FROM Member
         WHERE 
           MemberId=?
         ")) {

    $triptype="NULL";
    $stime=date('Y-m-d H:i:s', strtotime($newevent->starttime));
    $etime=empty($newevent->endtime)?null:date('Y-m-d H:i:s', strtotime($newevent->endtime));
    $stmt->bind_param(
        'sssiisssss',
        $newevent->boat_category->id,
        $stime,
        $etime,
//      $triptype, TRIPTYPE not implemented
        $newevent->distance,
        $newevent->max_participants,
        $newevent->location,
        $newevent->name,
        $newevent->category->name,
        $newevent->comment,
        $cuser) ||  die("create event BIND errro ".mysqli_error($rodb));

    error_log("NOW EXE");
    if (!$stmt->execute()) {
        $error=" event exe error ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create event insert error: ".mysqli_error($rodb);
    } 
}


$rd=$rodb->query("SELECT LAST_INSERT_ID() as event_id FROM DUAL")->fetch_assoc() or die("Error in query: " . mysqli_error($db));
$event_id=$rd["event_id"];
error_log(" event_id: $event_id");

if (empty($error) and $newevent->owner_in) {
    if ($ostmt = $rodb->prepare("INSERT INTO event_member(member,event,enter_time,role)
         SELECT Member.id, LAST_INSERT_ID() ,NOW(),'owner' FROM Member WHERE MemberId=?")) {
        $ostmt->bind_param('s',$cuser) ||  $error=mysqli_error($rodb);        
        if ($ostmt->execute()) {
            error_log("INSERTED member");
        } else {
            $error="event Insert DB STMT  error: ".mysqli_error($rodb);
            $message=$message."\n"."create event owner insert error: ".mysqli_error($rodb);
            error_log($error);
        }                
    } else {
        $error="event owner Insert error: ".mysqli_error($rodb);
    }
}


// members

$toMemberIds=array();

if (empty($error)) {
    if ($istmt = $rodb->prepare("INSERT INTO event_invitees(member,event,role)
         SELECT Member.id, LAST_INSERT_ID(),'member' From Member WHERE MemberId=?")) {
        
        foreach ($newevent->invitees as $invitee) {
            error_log("INVI".print_r($invitee,true));
            $toMemberIds[]=$invitee->id;
            $istmt->bind_param('s',$invitee->id) ||  $error=mysqli_error($rodb);        
            if ($istmt->execute()) {
                error_log("OK, inserted inviteee");
            } else {
                $error="event invitee Insert DB STMT  error: ". $rodb->error;
                error_log($error);
                $message="$message \n $error";
            }
        }    
    } else {
        $error="event invitee Insert error: ".mysqli_error($rodb);
    }
}
    
if (!empty($newevent->forum)) {
    $message=$newevent->comment;
    $title="Invitation til $newevent->name";
    send_to_forum($newevent->forum,$message,$title);
}


// Now email

$body="Invitation til http://aftaler/frontend/event/#!timeline?event=$event_id " . $newevent->comment;

$mail_headers = array(
    'From'                      => "Roaftaler i Danske Studenters Roklub <elgaard@agol.dk>",
    'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
    'Content-Transfer-Encoding' => "8bit",
    'Content-Type'              => 'text/plain; charset="utf8"',
    'Date'                      => date('r'),
    'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
    'MIME-Version'              => "1.0",
    'X-Mailer'                  => "PHP-Custom",
    'Subject'                   => "Invitation til $newevent->name"
);

$toEmails=array();
$qMarks = str_repeat('?,', count($toMemberIds) - 1) . '?';
$stmt = $rodb->prepare("SELECT email FROM Member WHERE MemberId IN ($qMarks) AND email IS NOT NULL") or die("Error in location query: " . mysqli_error($rodb));


$mi=array();
foreach($toMemberIds as $key => $mr) {
    $mi[$key] = &$toMemberIds[$key];
}

array_unshift($mi, str_repeat('s', count($toMemberIds)));
error_log("Mi: ".print_r($mi,true));
call_user_func_array(array($stmt, 'bind_param'), $mi); 
$stmt->execute() or die("Error in location query: " . mysqli_error($rodb));
$result= $stmt->get_result() or die("Error in location query: " . mysqli_error($rodb));
while ($rower = $result->fetch_assoc()) {
    error_log(print_r($rower,true));
    $toEmails[] = $rower['email'];
}

error_log("Sen INVI $body\n".print_r($toEmails,true) );
$smtp = Mail::factory('sendmail', array ());

$mail_status = $smtp->send($toEmails, $mail_headers, $body);

if (PEAR::isError($mail_status)) {
    $res["status"]="error";
    $res["message"] = "Kunne ikke sende invitationer til $toEmails: " . $mail_status->getMessage();
}


if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
echo json_encode($res);
?> 
