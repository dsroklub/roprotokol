<?php
include("../../rowing/backend/inc/common.php");

$s="SELECT Member.MemberId as member_id, forum.name as forum, owner.MemberId as owner, forum.description, forum_subscription.role
    FROM Member, forum_subscription, forum LEFT JOIN Member owner ON owner.id=forum.owner
    WHERE forum.name=forum_subscription.forum AND
      forum_subscription.member=Member.id AND
     Member.MemberId=?";

//echo "$s\n";
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s", $cuser);
    $stmt->execute();
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
     echo '[';
     $first=1;
     while ($row = $result->fetch_assoc()) {
         if ($first) $first=0; else echo ',';	  
         echo json_encode($row,	JSON_PRETTY_PRINT);
     }
     echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}
