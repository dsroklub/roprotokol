<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="maxkammerater.csv"');

$s='SELECT Member.MemberID,Concat(Member.FirstName," ",Member.LastName) as name,COUNT(distinct tmo.member_id) as nummates
FROM Member, Trip,TripMember tm, TripMember tmo
WHERE tm.TripID=Trip.id AND tm.member_id=Member.id AND tmo.TripID=Trip.id AND Member.id!=tmo.member_id 
AND YEAR(Trip.OutTime)=YEAR(NOW())
GROUP By Member.id
ORDER BY nummates DESC
LIMIT 10;
';

$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));;
 while ($row = $result->fetch_assoc()) {
	  echo $row["name"].",".$row["nummates"]."\n";
 }
?> 

