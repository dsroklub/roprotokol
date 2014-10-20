<?php
ini_set('default_charset', 'utf-8');

if(!isset($_SESSION))  session_start();
$rodb=new mysqli("localhost","root","","roprotokol");

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 

if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

//$WhichYear=strftime("%Y",time());
$WhichYear="2013";
    $s="SELECT Sum(Meter/1000) AS Km ,Medlem.MedlemID, Medlem.Fornavn, Medlem.Efternavn  
    FROM Gruppe,Trip,TripMember,Båd,Medlem 
    WHERE 
      Trip.TripID = TripMember.TripID AND
      Medlem.MedlemID = TripMember.MemberID AND
      Båd.BådID = Trip.BoatID AND     
      Gruppe.GruppeID = Båd.FK_GruppeID AND
      (((Year(OutTime))=".$WhichYear.") AND ((Gruppe.FK_BådKategoriID)=2)) 
    GROUP BY Medlem.MedlemID;";

$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
