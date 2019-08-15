<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$s="
SELECT CONCAT(mf.FirstName,' ',mf.LastName) as sender, DATE_FORMAT(forum_message.created,'%Y-%m-%dT%T') as created, forum_message.forum as source, 0 as event, 'forum' as type, CONCAT('f',forum_message.id) as msgid, forum_message.id, subject, forum_message.message as body, 1 as current,sticky
  FROM Member mf, Member,forum_message,forum_subscription
  WHERE
    forum_message.forum=forum_subscription.forum AND
    forum_subscription.member=Member.id AND
    mf.id=member_from AND
    Member.MemberId=?

UNION
SELECT CONCAT(mf.FirstName,' ',mf.LastName) as sender, DATE_FORMAT(event_message.created,'%Y-%m-%dT%T') as created, event.name as source, event.id as event,'event' as type, CONCAT('e',event_message.id) as msgid,event_message.id,subject, event_message.message as body, event.end_time>NOW() as current,-1 as sticky
  FROM Member, member_message, event_message LEFT JOIN  event ON event_message.event=event.id LEFT JOIN Member mf ON mf.id=member_from
  WHERE
    member_message.message=event_message.id AND
    member_message.member=Member.id AND
    Member.MemberId=?
UNION
SELECT CONCAT(mf.FirstName,' ',mf.LastName) as sender, DATE_FORMAT(private_message.created,'%Y-%m-%dT%T') as created, 'private' as source, NULL AS event,'private' AS type, CONCAT('p',private_message.id) as msgid,private_message.id,subject, private_message.message as body, 1 AS current,-1 as sticky
  FROM Member,member_message, private_message LEFT JOIN Member mf ON mf.id=member_from
  WHERE
    member_message.message=private_message.id AND
    member_message.member=Member.id AND
    Member.MemberId=?
ORDER BY sticky DESC,created DESC
";
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("sss", $cuser,$cuser,$cuser);
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
