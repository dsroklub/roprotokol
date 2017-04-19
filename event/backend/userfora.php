<?php
include("../../rowing/backend/inc/common.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$s="SELECT forum.name, forum.description FROM forum, forum_subscription, Member 
    WHERE Member.id=forum_subscription.member AND Member.MemberId=?";

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
