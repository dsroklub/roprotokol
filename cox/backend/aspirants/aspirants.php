<?php

$res=array ("status" => "ok");

include("../../../rowing/backend/inc/common.php");
include("utils.php");


//error_log("RSS " . print_r($_SERVER['PHP_AUTH_USER'],true));

$s="SELECT JSON_MERGE(
    JSON_OBJECT(
      'member_id',Member.MemberID, 
      'name', CONCAT(FirstName,' ',LastName),
      'wish',wish,
      'phone',team_requests.phone,
      'email',team_requests.email,
      'team',team,
      'preferred_time',preferred_time, 
      'preferred_intensity',preferred_intensity,
      'activities',activities, 
      'comment',comment
   ),
   CONCAT(
     '{', JSON_QUOTE('passes'),': [',
     GROUP_CONCAT(JSON_OBJECT(
        'pass',course_requirement.name,
        'dispensation',IFNULL(dispensation,0),
        'passes',ifNULL(passed,'')
     )),
   ']}')
   ) AS json
    FROM team_requests 
         LEFT JOIN Member on Member.id=team_requests.member_id
         JOIN course_requirement 
         LEFT JOIN course_requirement_pass ON course_requirement.name=course_requirement_pass.requirement AND course_requirement_pass.member_id=Member.id
   WHERE NOT EXISTS (SELECT 'x' FROM MemberRights WHERE MemberRight='cox' AND Member.id=MemberRights.member_id)
   GROUP By team_requests.member_id
   ORDER By Member.MemberId
";

$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res);
}

?> 
