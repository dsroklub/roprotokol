<?php
include("inc/common.php");

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

$s="SELECT BådID as id,
           Båd.Navn as name,
           Gruppe.Pladser as spaces,
           Båd.Beskrivelse as description,
           Gruppe.Navn as category,
           BådKategori.Navn as boattype,
           Båd.Location as location,
           Båd.Placement as placement,
           COALESCE(MAX(Skade.Grad),0) as damage,
           MAX(Trip.TripID) as trip,
           MAX(Trip.OutTime) as outtime,
           MAX(Trip.ExpectedIn) as expected_in
    FROM Båd
         INNER JOIN Gruppe ON (GruppeID=FK_GruppeID)
         INNER JOIN BådKategori ON (BådKategori.BådKategoriID = Gruppe.FK_BådKategoriID)
         LEFT OUTER JOIN Skade ON (Skade.FK_BådID=Båd.BådID AND Skade.Repareret IS NULL)
         LEFT OUTER JOIN Trip ON (Trip.BoatID = Båd.BådID AND Trip.Intime IS NULL)
    WHERE 
         Båd.Decommissioned IS NULL
    GROUP BY
       BådID,
       Båd.Navn,
       Gruppe.Pladser,
       Båd.Beskrivelse,
       Gruppe.Navn,
       Båd.Location,
       Båd.Placement
    ";


// echo $s;
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
