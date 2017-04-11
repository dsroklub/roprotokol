<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="racerkaniner.csv"');

$s='SELECT MemberID,FirstName,LastName, ROUND(Sum(Meter)/1000,1) AS Distance, COUNT("x") AS gange  
FROM Trip,TripMember, Member, TripType 
WHERE Trip.id=TripMember.TripID AND TripType.id=Trip.TripTypeID AND Member.id=TripMember.member_id AND YEAR(OutTime)=YEAR(NOW()) AND TripType.Name="Racerkanin" Group By Member.id ORDER BY FirstName,LastName
';
$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));;
 while ($row = $result->fetch_assoc()) {
	  echo $row["FirstName"]." ".$row["LastName"].",".$row["MemberID"].",".$row["Distance"].",".$row["gange"]."\n";
 }
?> 

