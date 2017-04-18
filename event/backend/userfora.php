<?php
include("../../rowing/backend/inc/common.php");
dbfetch($rodb,"forum",['*']);

$s="SELECT forum.name, forum.description FROM forum, forum_subscription, Member 
    WHERE Member.id=forum_subscription.member AND Member.MemberId=?";

$result=$db->query($s) or die("Error in stat query: " . mysqli_error($db));
echo '[';
$first=1;
while ($row = $result->fetch_assoc()) {
    if ($first) $first=0; else echo ',';	  
    echo json_encode($row,	JSON_PRETTY_PRINT);
}
echo ']';

