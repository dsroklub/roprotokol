<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
$sql="
SELECT JSON_QUOTE(Boat.Name) as boat,
  CONCAT('[',GROUP_CONCAT(JSON_OBJECT('subject',subject,'message',message)),']'
  )
 as json
 FROM Boat,forum,forum_message
WHERE forum.boat=Boat.Name and forum_message.forum=forum.name AND sticky>0 AND deleted IS NULL
GROUP BY Boat.Name
";
$result=$rodb->query($sql) or dbErr($rodb,$res,"boat notes");
echo '{';
$first=false;
 while ($row = $result->fetch_assoc()) {
     if ($first) {echo ",\n";$first=true;}
     echo $row["boat"].":". $row["json"]."\n";
}
echo '}';
$rodb->close();
invalidate('messages');
