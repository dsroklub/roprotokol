<?php
include("../../rowing/backend/inc/common.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$s="SELECT forum.name as forum,forum.description, Member.MemberId as owner,is_open, forum_subscription.role
    FROM Member, forum JOIN Member m LEFT JOIN forum_subscription ON (forum.name=forum_subscription.forum AND forum_subscription.member=m.id)
    WHERE Member.id=forum.owner and m.MemberId=?" ;

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s", $cuser);
    $stmt->execute();
     $result= $stmt->get_result() or die("Error in fora query: " . mysqli_error($rodb));
     
     echo '[';
     $first=1;
     while ($row = $result->fetch_assoc()) {
         if ($first) $first=0; else echo ',
';	  
         echo json_encode($row);
     }
     echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}
$rodb->close();
?> 
