<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="langtursstyrmaend.csv"');
$s='SELECT Concat(FirstName," ",LastName) as name, MemberID,MemberRight
    From Member,MemberRights 
    WHERE member_id=Member.id AND (MemberRight="longdistance" OR MemberRight="longdistancetheory")
    ORDER BY MemberRight,name';

$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));;
 while ($row = $result->fetch_assoc()) {
	  echo $row["name"].",".$row["MemberID"].",".$row["MemberRight"]."\n";
 }
?> 
