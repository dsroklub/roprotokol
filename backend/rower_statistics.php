<?php
ini_set('default_charset', 'utf-8');

if(!isset($_SESSION))  session_start();
$rodb=new mysqli("localhost","roprotokol","","roprotokol");

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}
$season=date('Y');
//$season='2013';
$rodb->query("set @rn = 0");
    $s="SELECT Sum(Meter) AS distance ,Medlem.MedlemID as id, Medlem.Fornavn as firstname, Medlem.Efternavn as lastname 
    FROM Gruppe,Trip,TripMember,Båd,Medlem 
    WHERE 
      Trip.TripID = TripMember.TripID AND
      Medlem.MedlemID = TripMember.MemberID AND
      Båd.BådID = Trip.BoatID AND     
      Gruppe.GruppeID = Båd.FK_GruppeID AND
      (((Year(OutTime))=".$season.") AND ((Gruppe.FK_BådKategoriID)=2)) 
    GROUP BY Medlem.MedlemID 
    ORDER BY distance desc";
// fixme also for kayaks

//echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $rn=1;
 while ($row = $result->fetch_assoc()) {
	  if ($rn>1) echo ',';
      $row['rank']=$rn;
      $row['distance']=(int)$row['distance']; //  bad hack
	  echo json_encode($row);
      $rn=$rn+1;
}
echo ']';
$rodb->close();
?> 
