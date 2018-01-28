<?php

require_once("Mail.php");

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

function post_message($toEmails,$subject,$message) {
    global $rodb;
//    error_log("post message $toEmail, $subject\n$message");
    $res=array ("status" => "ok");
    $error=null;
    $warning=null;
    $smtp = Mail::factory('sendmail', array ());
    $mail_headers = array(
        'From'                      => "Roaftaler i Danske Studenters Roklub <aftaler_noreply@danskestudentersroklub.dk>",
        'Content-Transfer-Encoding' => "8bit",
        'Content-Type'              => 'text/plain; charset="utf8"',
        'Date'                      => date('r'),
        'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
        'MIME-Version'              => "1.0",
        'X-Mailer'                  => "PHP-Custom",
        'Subject'                   => "$subject"
    );
    

    error_log("now send mail");
    $mail_status = $smtp->send($toEmails, $mail_headers, $message);
    
    if (PEAR::isError($mail_status)) {
        $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage();
    }
        
    if ($error) {
        error_log("messagelib: $error");
        $res['message']=$message;
        $res['status']='error';
        $res['error']=$error;
    } else if ($warning) {
        $res["status"]="warning";
        $res['warning']=$warning;
    }
    return $res;
}


function post_private_message($memberId,$subject,$message) {
    global $rodb;
    $stmt = $rodb->prepare("SELECT email FROM Member WHERE Member.MemberId=?");
    $stmt->bind_param('s',$memberId) or die("{\"status\":\"Error in event private message query bind: " . mysqli_error($rodb) ."\"}");
    $stmt->execute() or die("{\"status\":'Error in private message exe query: " . mysqli_error($rodb) ."\"}");
    $result= $stmt->get_result() or die("{\"status\":'Error in private message query: " . mysqli_error($rodb) ."\"}");
    if ($email=$result->fetch_assoc()) {
        return post_message([$email],$subjet,$message);
    } else {
        error_log("member not found");
    }    
}

function post_event_message($eventId,$subject,$message) {
    global $rodb;
    global $cuser;
    $stmt = $rodb->prepare(
        "SELECT DISTINCT email 
     FROM Member,event_member
     WHERE Member.id=event_member.member AND event_member.event=?");
    
    $stmt->bind_param('i',$eventId) or die("{\"status\":\"Error in evet message query bind: " . mysqli_error($rodb) ."\"}");
    $stmt->execute() or die("{\"status\":'Error in event message exe query: " . mysqli_error($rodb) ."\"}");
    $result= $stmt->get_result() or die("{\"status\":'Error in event message query: " . mysqli_error($rodb) ."\"}");

    $toEmails=array();
    while ($rower = $result->fetch_assoc()) {
        error_log(print_r($rower,true));
        $toEmails[] = $rower['email'];
    }
    $result->free();

    $res=post_message($toEmails,$subject,$message);
    error_log("INSERT EVE MESSAGE $eventId, $subject, $message, $cuser");
    if ($stmt = $rodb->prepare(
        "INSERT INTO event_message(member_from, event, created, subject, message)
         SELECT mf.id,?,NOW(),?,?
         FROM Member mf
         WHERE 
           mf.MemberId=?")) {        
        $stmt->bind_param(
            'ssss',
            $eventId,
            $subject,
            $message,
            $cuser) ||  die("create event message BIND errro ".mysqli_error($rodb));        
        if (!$stmt->execute()) {
            $error=" message event error ".mysqli_error($rodb);
            error_log($error);
            $message=$message."\n"."event message DB error: ".mysqli_error($rodb);
        } else {
            $error=$rodb->error;
            error_log("event send insert db $error");
        } 
    }

    if ($stmt = $rodb->prepare(
        "INSERT INTO member_message(member, message)
             SELECT Member.id,LAST_INSERT_ID()
             FROM Member, event_member
             WHERE Member.id=event_member.member AND event_member.event=?")) {
        $stmt->bind_param(
            's',
            $eventId) ||  die("create event message BIND errro ".mysqli_error($rodb));        
        if (!$stmt->execute()) {
            $error=" message event membererror ".mysqli_error($rodb);
            error_log($error);
            $message=$message."\n"."event messagelib member DB error: ".mysqli_error($rodb);
        } 
    }
    return $res;
    invalidate("message");   
}

function post_forum_message($forum,$subject,$message) {
    $res=array ("status" => "ok");
    global $rodb;
    global $cuser;
    $stmt = $rodb->prepare(
        "SELECT DISTINCT email 
     FROM Member,forum_subscription
     WHERE Member.id=forum_subscription.member AND forum_subscription.forum=?");
    
    $stmt->bind_param('s',$forum) or die("{\"status\":\"Error in message query bind: " . mysqli_error($rodb) ."\"}");
    $stmt->execute() or die("{\"status\":'Error in message exe query: " . mysqli_error($rodb) ."\"}");
    $result= $stmt->get_result() or die("{\"status\":'Error in message query: " . mysqli_error($rodb) ."\"}");

    $toEmails=array();
    while ($rower = $result->fetch_assoc()) {
        error_log("pfmail -> " . print_r($rower,true));
        $toEmails[] = $rower['email'];
    }
    $result->free();


    $msgid="error";
    if ($stmt = $rodb->prepare(
        "INSERT INTO forum_message(member_from, forum, created, subject, message)
         SELECT mf.id,?,NOW(),?,?
         FROM Member mf
         WHERE 
           mf.MemberId=?")) {        
        $stmt->bind_param(
            'ssss',
            $forum,
            $subject,
            $message,
            $cuser) ||  die("create forum message BIND errro ".mysqli_error($rodb));
        
        error_log("NOW EXE");
        if ($stmt->execute()) {            
            $msgid=$rodb->query("SELECT LAST_INSERT_ID() AS msgid")->fetch_assoc()["msgid"];                
        } else {
            $error=" message forum error ".mysqli_error($rodb);
            error_log($error);
            $message=$message."\n"."forum message DB error: ".mysqli_error($rodb);
        } 
    }

    $cuser=$_SERVER['PHP_AUTH_USER'];
    $fromUser=$rodb->query("SELECT CONCAT(FirstName,' ',LastName) as name FROM Member WHERE MemberId=$cuser")->fetch_assoc()["name"];
    $res=post_message(
        $toEmails,
        $subject,
        "Fra $fromUser ($cuser)\n\n". $message .
        "\n\nSendt fra DSR aftaler\nhttps://aftaler.danskestudentersroklub.dk/\nForum: $forum\nhttps://aftaler.danskestudentersroklub.dk/frontend/event/#!message/?message=f$msgid"
    );
    invalidate("message");   
    return $res;    
}
