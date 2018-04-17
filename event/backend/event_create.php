<?php
require("inc/utils.php");
include("../../rowing/backend/inc/common.php");
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
if ($stmt = $rodb->prepare(
        "INSERT INTO event(owner, boats, start_time, end_time, distance, max_participants, location, name, category, comment,open,auto_administer,destination)
         SELECT Member.id, ?,CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?,?,?,?,?,?,?,?,? 
         FROM Member
         WHERE 
           MemberId=?
         ")) {

    $triptype="NULL";
    if (empty($newevent->destination)) {
        $destination=null;
    } else {
        $destination=$newevent->destination->name;
    }
        
    $stime=date('Y-m-d H:i:s', strtotime($newevent->starttime));
    $etime=empty($newevent->endtime)?null:date('Y-m-d H:i:s', strtotime($newevent->endtime));
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
        $newevent->open,
        $newevent->automatic,
        $destination,
        $cuser) ||  die("create event BIND error ".mysqli_error($rodb));

    if ($stmt->execute()) {
        error_log("created EVENT");
    } else {
        $error=" event exe error ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."create event insert error: ".mysqli_error($rodb);
    } 
} else {
    $error=" event exe error ".mysqli_error($rodb);
    error_log($error);
}


$rd=$rodb->query("SELECT LAST_INSERT_ID() as event_id FROM DUAL")->fetch_assoc() or die("Error in query: " . mysqli_error($db));
$event_id=$rd["event_id"];
error_log(" event_id: $event_id ".print_r($rd,true));

if (empty($error) and $newevent->owner_in) {
    if ($ostmt = $rodb->prepare("INSERT INTO event_member(member,event,enter_time,role)
         SELECT Member.id, ? ,NOW(),'owner' FROM Member WHERE MemberId=?")) {
        $ostmt->bind_param('is',$event_id, $cuser) ||  $error=mysqli_error($rodb);        
        if ($ostmt->execute()) {
            error_log("inserted owner $cuser");
        } else {
            $error="event Insert DB STMT  error: ".mysqli_error($rodb);
            $message=$message."\n"."create event owner insert error: ".mysqli_error($rodb);
            error_log($error);
        }                
    } else {
        $error="event owner: Insert error: ".mysqli_error($rodb);
    }
}

// members

$toMemberIds=array();
if (empty($error)) {
    if ($istmt = $rodb->prepare("INSERT INTO event_invitees(member,event,role)
         SELECT Member.id, LAST_INSERT_ID(),'member' From Member WHERE MemberId=?")) {
        
        foreach ($newevent->invitees as $invitee) {
            $toMemberIds[]=$invitee->id;
            $istmt->bind_param('s',$invitee->id) ||  $error=mysqli_error($rodb);        
            if ($istmt->execute()) {
            } else {
                $error="event invitee Insert DB STMT  error: ". $rodb->error;
                $message="$message \n $error";
            }
        }    
    } else {
        $error="event invitee Insert error: ".mysqli_error($rodb);
    }
}
    
// Now Store message
$subject="Invitation til $newevent->name";
$emailbody="Invitation til https://aftaler.danskestudentersroklub.dk/frontend/event/#!timeline?event=$event_id " . $newevent->comment;
$body="Invitation til https://aftaler.danskestudentersroklub.dk/frontend/event/#!timeline?event=$event_id " . $newevent->comment;
if ($stmt = $rodb->prepare(
        "INSERT INTO event_message(member_from, event, created, subject, message)
         SELECT mf.id, ?,NOW(),?,?
         FROM Member mf
         WHERE 
           mf.MemberId=?")) {
    $stmt->bind_param(
        'ssss',
        $event_id,
        $subject,
        $body,
        $cuser) ||  die("create forum message BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" message forum error ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."forum message DB error: ".mysqli_error($rodb);
    } 
}

$iforas="";
if (!empty($newevent->fora)) {
    $ifors=array();


    foreach ($newevent->fora as $forum) {
        if ($stmt = $rodb->prepare("INSERT INTO event_forum(event,forum) VALUES(?,?)")) {
            $stmt->bind_param('is',$event_id,$forum->forum);
            if (!$stmt->execute()) {
                $error=" event,forum error ".mysqli_error($rodb);
                error_log($error);
            }     
        }        
        $ifora[]="'".$rodb->real_escape_string("$forum->forum")."'";
    }
    $iforas=implode(",",$ifora);
}

if (!empty($newevent->fora)) {
    if ($stmt = $rodb->prepare(
        "INSERT INTO member_message(member, message)
         SELECT DISTINCT Member.id,LAST_INSERT_ID()
         FROM Member, forum_subscription
         WHERE 
             Member.id=forum_subscription.member AND 
             forum_subscription.forum IN ($iforas)")) {
        if ($stmt->execute()) {
            invalidate("message");
        } else {
            $error=" message forum member error ".mysqli_error($rodb);
            error_log($error);
            $message=$message."\n"."forum message member DB error: ".mysqli_error($rodb);
        } 
    } else {
        $error=" event create forum error IF=$iforas: ". $rodb->error;
        error_log($error);
    }
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
    
     if ($stmt=$rodb->prepare(
        "INSERT INTO member_message(member, message)
         SELECT Member.id, LAST_INSERT_ID()
         FROM Member
         WHERE
              MemberId IN ($qMarks) AND Member.id NOT IN 
               (SELECT member from member_message m2 WHERE m2.message=LAST_INSERT_ID())")) {
        call_user_func_array( array($stmt, 'bind_param'), $ml); 
        $stmt->execute() or die("Error in mm INSERT query: " . mysqli_error($rodb));
        invalidate("message");
    } else {
        $error="Error in mm prepare query: $rodb->error";
        error_log($error);
    }
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
         'Subject'                   => $subject
     );

     $toEmails=array();
     $stmt=$rodb->prepare("SELECT email FROM Member 
                      WHERE MemberId IN ($qMarks) AND email IS NOT NULL") or die("Error in location query: ". mysqli_error($rodb));     
     call_user_func_array(array($stmt, 'bind_param'), $mi); 
     $stmt->execute() or die("Error in location query: " . mysqli_error($rodb));
     $result= $stmt->get_result() or die("Error in location query: " . mysqli_error($rodb));
     while ($rower = $result->fetch_assoc()) {
//         error_log(print_r($rower,true));
         $toEmails[] = $rower['email'];
     }
     
     $smtp = Mail::factory('sendmail', array ());     
     $mail_status = $smtp->send($toEmails, $mail_headers, $emailbody);

     if (PEAR::isError($mail_status)) {
         $res["status"]="warning";
         $res["message"] = "Kunne ikke sende invitationer til $toEmails: " . $mail_status->getMessage();
     }
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
invalidate("message");
echo json_encode($res);
?> 
