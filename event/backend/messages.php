<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$s="
SELECT CONCAT(mf.FirstName,' ',mf.LastName) as name, forum_message.created, forum_message.forum as source, 'forum' as type,42 as msgid,subject, forum_message.message as body
  FROM Member mf, Member,forum_message,forum_subscription
  WHERE 
    forum_message.forum=forum_subscription.forum AND 
    forum_subscription.member=Member.id AND
    mf.id=member_from AND
    Member.MemberId=?

UNION 
SELECT CONCAT(mf.FirstName,' ',mf.LastName) as name, event_message.created, event.name as source, 'event' as type, event_message.id as msgid,subject, event_message.message as body
  FROM Member mf, Member, member_message, event_message LEFT JOIN  event ON event_message.event=event.id
  WHERE 
    member_message.message=event_message.id AND
    member_message.member=Member.id AND
    mf.id=member_from AND
    Member.MemberId=?
ORDER BY created DESC
";
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("ss", $cuser,$cuser);
    $stmt->execute();
    $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
    if ($result) {
        echo '[';
        $first=1;
        while ($row = $result->fetch_assoc()) {
            if ($first) $first=0; else echo ',';	  
            echo json_encode($row,JSON_PRETTY_PRINT);
        }
        echo ']';
    } else {
        dbErr($rodb,$res,"messages");
        echo json_encode($res,JSON_PRETTY_PRINT);
    }
} else {
    dbErr($rodb,$res,"messages query");
}

?> 
