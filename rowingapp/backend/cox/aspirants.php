<?php

$res=array ("status" => "ok");

set_include_path(get_include_path().':..');
include("inc/common.php");
$s='SELECT Member.MemberId as member_id, wish, team_requests.phone,team_requests.email,team,CONCAT(Member.FirstName," ",Member.LastName) as name, preferred_time, activities,comment
    FROM team_requests, Member where Member.id=team_requests.member_id
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
