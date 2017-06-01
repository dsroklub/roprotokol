<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$s="
SELECT Member.MemberId as member_id, CONCAT(Member.FirstName,' ',Member.LastName) as name, role
   FROM Member, forum_subscription
   WHERE forum_subscription.member_id=Member.id AND 
   forum_subscription.forum=?
   ORDER BY name
";

$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';	  
        echo json_encode($row,JSON_PRETTY_PRINT);
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}

?> 
