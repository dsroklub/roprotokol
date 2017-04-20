<?php
include("inc/common.php");
include("inc/utils.php");


$s="SELECT Trip.id, TripType.Name AS triptype, Boat.Name AS boat, Trip.Destination as destination, 
     DATE_FORMAT(Trip.InTime,'%Y-%m-%dT%T') as intime,DATE_FORMAT(Trip.OutTime,'%Y-%m-%dT%T') as outtime, DATE_FORMAT(Trip.ExpectedIn,'%Y-%m-%dT%T') as expectedintime,
     GROUP_CONCAT(Member.MemberID,':§§:', Concat(Member.FirstName,' ',Member.LastName) ORDER BY Seat SEPARATOR '££') AS rowers 
   FROM Trip, Boat, TripType, TripMember LEFT JOIN Member ON Member.id = TripMember.member_id  
   WHERE Boat.id=Trip.BoatID AND Trip.id=TripMember.TripID AND Trip.InTime IS NOT NULL AND TripType.id = Trip.TripTypeID  AND Trip.InTime  >= CURDATE() 
   GROUP BY Trip.id 
   ORDER BY Trip.id DESC";

if ($sqldebug) {
  echo $s;
  echo "\n";
}
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));

     echo '[';
     $first=1;
     while ($row = $result->fetch_assoc()) {
       if ($first) $first=0; else echo ',';
       $row['rowers']=multifield_array($row['rowers'],["member_id","name"]);
       echo json_encode($row,JSON_PRETTY_PRINT);
     }
     echo ']';
} else {
        $error="createtrip Member DB error: " . $rodb->error;
        error_log("OOOPS 2 $error");
}
$rodb->close();
?> 
