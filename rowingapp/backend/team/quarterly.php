<?php
if (isset($_GET["quarter"])) {
    $quarter=$_GET["quarter"];
} else {
    echo "please set quarter";
    exit(1);
}
include("inc/backheader.php");
header('Content-Disposition: filename="gymnastikQ'.$quarter.'.csv"');
set_include_path(get_include_path().':..');
include("inc/common.php");
header('Content-type: text/csv');

$s=
 'SELECT team.name AS team, classdate,team.description,CONCAT(FirstName," ",LastName) AS membername, Member.MemberID,KommuneKode,CprNo
  FROM team, team_participation, Member 
  WHERE team_participation.team=team.name AND Member.id=team_participation.member_id AND QUARTER(classdate)=? 
   AND (YEAR(classdate)=YEAR(NOW()) OR YEAR(classdate)=YEAR(NOW())-1)
  ';

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("i", $quarter);
    $stmt->execute();
     $result= $stmt->get_result() or die("Error in quarterly query: " . mysqli_error($rodb));
     echo "Deltager,Medlemsnr,hold,holddato,KommuneKode,CprNo\n";
     while ($row = $result->fetch_assoc()) {
         echo $row["membername"].",".$row["MemberID"].",".$row["team"].",".$row["classdate"].",".$row["KommuneKode"] . ",".$row["CprNo"] . "\n";
     }
}
?> 
