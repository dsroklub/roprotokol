<?php

include("../../../rowing/backend/inc/common.php");
$s='SELECT instruction_team.name,description, Member.MemberId as instructor, CONCAT(Member.FirstName," ", Member.LastName) as instructor_name,COUNT(team_requests.member_id) as occupancy
    FROM instruction_team LEFT JOIN team_requests ON team_requests.team=instruction_team.name, Member 
    WHERE Member.id=instruction_team.instructor 
    GROUP BY name,instructor_name, instructor
    ORDER BY name 
';
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
?> 
