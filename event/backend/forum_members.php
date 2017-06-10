<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$forum=sanestring($_REQUEST['forum']);

error_log("forum $forum");
$s="
SELECT forum_subscription.forum, forum.owner,Member.MemberId as member_id, CONCAT(Member.FirstName,' ',Member.LastName) as name, role
   FROM Member, forum_subscription,forum
   WHERE 
     forum_subscription.forum=forum.name AND
     forum_subscription.member=Member.id AND 
     forum_subscription.forum=?
   ORDER BY name
";
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s", $forum);
    $stmt->execute();

    $result= $stmt->get_result() or die("Error in forummember query: " . mysqli_error($rodb));
    if ($result) {
        echo '[';
        $first=1;
        while ($row = $result->fetch_assoc()) {
            if ($first) $first=0; else echo ',';	  
            echo json_encode($row,JSON_PRETTY_PRINT);
        }
        echo ']';
    } else {
        error_log("no forum members");
    }
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}

?> 
