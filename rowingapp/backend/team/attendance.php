<?php

set_include_path(get_include_path().':..');
include("inc/common.php");
$s=
 'SELECT team.name AS team, team.dayofweek,team.timeofday,team.description,CONCAT(FirstName," ",LastName) AS membername, Member.MemberID as memberid 
  FROM team, team_participation, Member 
  WHERE team_participation.team=team.name 
        AND team_participation.dayofweek=team.dayofweek
        AND team_participation.timeofday=team.timeofday
        AND Member.id=team_participation.member_id 
        AND classdate=CURDATE()';

$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));

echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';

?> 
