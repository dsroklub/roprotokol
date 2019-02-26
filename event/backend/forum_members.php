<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$forum=sanestring($_REQUEST['forum']);

//error_log("forum $forum");
$s="
SELECT JSON_MERGE(
  JSON_OBJECT(
  'forum', forum_subscription.forum,
   'owner', mo.MemberId,
   'member_id',Member.MemberId, 
   'name',CONCAT(Member.FirstName,' ',Member.LastName),
   'role', role,
   'comment',forum_subscription.comment,
   'hours',SUM(worklog.hours)
   ),
    CONCAT('{', JSON_QUOTE('log'),': [',
     IF(SUM(worklog.hours),
       GROUP_CONCAT(JSON_OBJECT(
      'workdate',DATE_FORMAT(worklog.workdate,'%Y-%m-%d'),
      'hours',worklog.hours,
      'work',worklog.work,
      'by',worklog.created_by,
      'created',worklog.created 
      )),''),
   ']}')
  )
     as json
   FROM Member mo, forum, 
     (Member JOIN forum_subscription on forum_subscription.member=Member.id)  LEFT JOIN worklog on worklog.forum=forum_subscription.forum AND worklog.member_id=Member.id
   WHERE 
     forum_subscription.forum=forum.name AND
     mo.id=forum.owner AND
     forum_subscription.forum=?
   GROUP BY Member.id,forum_subscription.forum,role,owner
   ORDER BY name
";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"forum q");
$stmt->bind_param("s", $forum) or dbErr($rodb,$res,"forummember bind");
$stmt->execute() or dbErr($rodb,$res,"forummember exe");

$result= $stmt->get_result() or dbErr($rodb,$res,"forummember r");
if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ",\n";
        echo $row["json"];
    }
    echo ']';
} else {
    error_log("no forum members");
}
