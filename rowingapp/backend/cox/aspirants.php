<?php

$res=array ("status" => "ok");

set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/utils.php");

$s="SELECT Member.MemberId as member_id, wish, team_requests.phone,team_requests.email,team,
    CONCAT(Member.FirstName,' ', Member.LastName) as name, preferred_time, preferred_intensity,
    activities, comment,
    GROUP_CONCAT(course_requirement.name,':§§:',IFNULL(dispensation,0), ':§§:',ifNULL(passed,'') ORDER BY course_requirement.name SEPARATOR '££') AS passes
    FROM team_requests 
         LEFT JOIN Member on Member.id=team_requests.member_id
         JOIN course_requirement 
         LEFT JOIN course_requirement_pass ON course_requirement.name=course_requirement_pass.requirement AND course_requirement_pass.member_id=Member.id
   GROUP By team_requests.member_id
   ORDER By Member.MemberId,passes
";

// print("SQL = $s");
//    ORDER BY team
$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';
        $row['passes']=multifield_array($row['passes'],["pass","dispensation","passed"]);
        echo json_encode($row);
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res);
}

?> 
