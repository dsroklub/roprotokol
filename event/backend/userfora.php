<?php
include("../../rowing/backend/inc/common.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$s="SELECT forum.name, forum.description,forum_subscription.role
    FROM (Member JOIN forum) LEFT JOIN forum_subscription ON forum_subscription.forum=forum.name AND forum_subscription.member=Member.id
    WHERE Member.MemberId=?";

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
}
