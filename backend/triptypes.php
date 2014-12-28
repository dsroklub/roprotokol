<?php
include("inc/common.php");

$s=<<<SQT
select Navn,Beskrivelse, GROUP_CONCAT(required_right,':',requirement) as rights from TurType, TripRights WHERE aktiv AND trip_type=Navn GROUP BY TurType.Navn;
SQT
;
// $s="SELECT TurTypeID as id, Navn as name FROM TurType ORDER BY id";


// echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';
      $rights=array();
      $rg=explode(',', $row['rights']);
      foreach ($rg as $ri) {
          $ris=explode(":",$ri);
          $rights[$ris[0]]=$ris[1];
      }
      $row['rights']=$rights;
	  echo json_encode($row,JSON_PRETTY_PRINT);
}
echo ']';
$rodb->close();
?> 
