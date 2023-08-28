<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$forum=sanestring($_REQUEST['forum']);

if (empty($forum)) {
    echo "missing forum\n";
    exit(-1);
}
//error_log("forum $forum");
$s="
SELECT JSON_OBJECT(
  'forum', forum_subscription.forum,
   'owner', mo.MemberId,
   'member_id',Member.MemberId,
   'name',CONCAT(Member.FirstName,' ',Member.LastName),
   'role', role,
   'work_todo', forum_subscription.work,
   'comment',forum_subscription.comment,
   'hours',SUM(worklog.hours),
   'log',IF(
      SUM(worklog.hours),
        JSON_ARRAYAGG(JSON_OBJECT(
          'start_time',DATE_FORMAT(worklog.start_time,'%Y-%m-%d'),
          'hours',worklog.hours,
           'work',worklog.work,
           'boat',worklog.boat,
           'by',worklog.created_by,
           'created',worklog.created
      ) ORDER BY start_time),JSON_ARRAY()
    )
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
output_json($result);
