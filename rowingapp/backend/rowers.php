<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT Member.MemberID AS id,CONCAT(FirstName,' ',LastName) AS name,Initials AS initials, GROUP_CONCAT(MemberRight,':§§:',argument, ':§§:',Acquired SEPARATOR '££') AS rights".
    "  FROM Member LEFT JOIN MemberRights on MemberRights.member_id=Member.id  WHERE Member.MemberID!='0' GROUP BY Member.id";

# Member.MemberID should not be necessary, non members should have MemberID=NULL, not 0

if ($sqldebug) {
    echo $s."<br>\n";
}
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
      $row['rights']=multifield_array($row['rights'],["member_right","arg","acquired"]);
	  echo json_encode($row,JSON_PRETTY_PRINT);
}
echo ']';
$rodb->close();
?> 
