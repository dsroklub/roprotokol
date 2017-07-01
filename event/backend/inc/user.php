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
        $error=" event status set ".mysqli_error($rodb);
        error_log($error);
    }
    return false;
}