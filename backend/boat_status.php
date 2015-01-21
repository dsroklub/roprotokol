<?php
include("inc/common.php");
header('Content-type: application/json');

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

$s="SELECT Boat.id,
           Boat.Name as name,
           COALESCE(MAX(Damage.Degree),0) as damage,
           MAX(Trip.TripID) as trip,
           MAX(Trip.OutTime) as outtime,
           MAX(Trip.ExpectedIn) as expected_in
    FROM Boat
         LEFT OUTER JOIN Damage ON (Damage.Boat=Boat.id AND Damage.Repaired IS NULL)
         LEFT OUTER JOIN Trip ON (Trip.BoatID = Boat.id AND Trip.Intime IS NULL)
    WHERE 
         Boat.Decommissioned IS NULL
    GROUP BY
       Boat.id,
       Boat.Name
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
