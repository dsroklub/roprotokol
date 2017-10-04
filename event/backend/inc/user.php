<?php

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $currentuser=$_SERVER['PHP_AUTH_USER'];
}//$currentuser="7843";

error_log("Current User $currentuser");

function check_event_owner($eventId) {
    global $currentuser;
    global $rodb;
    if ($stmt = $rodb->prepare(
        "SELECT Member.id
         FROM event, Member
         WHERE event.id=? AND MemberId=? AND event.owner=Member.id")
    ) {
        
        $stmt->bind_param(
            'ss',
            $eventId,
            $currentuser
        ) ||  die("set event stautsBIND errro ".mysqli_error($rodb));
        
        if ($stmt->execute()) {
            error_log("set evt status set OK");
            $result= $stmt->get_result() or die("Error in event owner check: " . mysqli_error($rodb));
            if (count($result)==1) {
                return true;
            }        
        } else {
            $error=" evt status set exe ".mysqli_error($rodb);
            $message=$message."\n"."role update error: ".mysqli_error($rodb);
        }
    } else {
        $error=" check even owner ".mysqli_error($rodb);
        error_log($error);
    }
    return false;
}


function check_forum_owner($forum) {
    global $currentuser;
    global $rodb;
    if ($stmt = $rodb->prepare(
        "SELECT Member.id
         FROM forum_subscription, Member
         WHERE 
           forum_subscription.forum=? AND MemberId=? AND forum_subscription.member=Member.id AND forum_subscription.role='admin'
         UNION 
            SELECT Member.id FROM forum,Member where forum.owner=Member.id AND Member.MemberID=? AND forum.name=?
            "
    )
    ) {        
        $stmt->bind_param(
            'ssss',
            $forum->forum,
            $currentuser,
            $currentuser,
            $forum->forum
        ) ||  die("check forum owner errro ".mysqli_error($rodb));
        
        if ($stmt->execute()) {
            error_log("check forum owner OK");
            $result= $stmt->get_result() or die("Error in forum owner check: " . mysqli_error($rodb));
            if (count($result)==1) {
                return true;
            }        
        } else {
            $error=" forum status set exe ".mysqli_error($rodb);
            $message=$message."\n"."role update error: ".mysqli_error($rodb);
        }
    } else {
        $error=" forum check user ".mysqli_error($rodb);
        error_log($error);
    }
    return false;
}