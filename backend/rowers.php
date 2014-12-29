<?php
include("inc/common.php");
include("inc/utils.php");

$s="SELECT Medlemsnr as id,CONCAT(Fornavn,' ',Efternavn) as name,Initialer as initials, GROUP_CONCAT(MemberRight,':',argument) as rights".
    "  FROM Medlem,MemberRights Where MemberRights.MemberID=Medlem.MedlemID GROUP BY MemberID";


echo $s."<br>";
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
