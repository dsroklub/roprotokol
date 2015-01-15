<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Member.MemberID AS id,CONCAT(FirstName,' ',LastName) AS name,Initials AS initials, GROUP_CONCAT(MemberRight,':§§:',argument SEPARATOR '££') AS rights".
    "  FROM Member,MemberRights WHERE MemberRights.MemberID=Member.id  GROUP BY Member.MemberID";


// echo $s."<br>";
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
      $row['rights']=multifield($row['rights']);
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
