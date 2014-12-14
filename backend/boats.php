<?php
include("inc/common.php");

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

    $s="SELECT BådID as id,Båd.Navn as name,Gruppe.Pladser as spaces,Båd.Beskrivelse as description,Gruppe.Navn as category, BådKategori.Navn as boattype
    FROM Båd,Gruppe, BådKategori
    WHERE GruppeID=FK_GruppeID
    AND BådKategori.BådKategoriID =  Gruppe.FK_BådKategoriID";


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
