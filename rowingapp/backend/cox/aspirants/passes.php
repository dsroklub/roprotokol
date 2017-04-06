<?php

$res=array ("status" => "ok");

include("../inc/common.php");
$s='SELECT Member.MemberId as member_id, requeirement,CONCAT(Member.FirstName," ",Member.LastName) as name
    FROM course_requirement_pass, Member where Member.id=course_requirement_pass.member_id
    ORDER BY team
';
$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';	  
        echo json_encode($row);
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res);
}

?> 
