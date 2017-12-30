<?php
include("inc/common.php");
include("inc/utils.php");


// TODO when we can use Mysql 8 replace with JSON_ARRAYAGG etc
$s="SELECT JSON_MERGE(
    JSON_OBJECT(
     'id',Trip.id, 
      'triptype', TripType.Name, 
      'boat',Boat.Name,
      'destination',Trip.Destination, 
      'intime',DATE_FORMAT(Trip.InTime,'%Y-%m-%dT%T'),
      'outtime',DATE_FORMAT(Trip.OutTime,'%Y-%m-%dT%T'),
      'expectedintime', DATE_FORMAT(Trip.ExpectedIn,'%Y-%m-%dT%T')
     ),
   CONCAT('{\"rowers\" : [',
     GROUP_CONCAT(JSON_OBJECT('member_id', Member.MemberID, 'name', CONCAT(Member.FirstName,' ',Member.LastName)) ORDER BY Seat),']}')
) AS json
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
       echo $row['json'];
     }
     echo ']';
} else {
        $error="createtrip Member DB error: " . $rodb->error;
        error_log("OOOPS 2 $error");
}
$rodb->close();
?> 
