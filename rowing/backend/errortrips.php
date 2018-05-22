<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
  
$sql="(SELECT Error_Trip.Trip,Error_Trip.id as error_id,JSON_MERGE(
    JSON_OBJECT(
     'reason',ReasonForCorrection,
     'id', Error_Trip.id,
     'Trip',Error_Trip.Trip,
     'Reporter', Error_Trip.Reporter,
     'DeleteTrip',DeleteTrip,
     'boat', Boat.Name,
     'triptype', TripType.Name,
     'destination', Error_Trip.Destination,
     'created', Error_Trip.CreatedDate,
     'distance', Error_Trip.Distance,
     'intime', DATE_FORMAT(TimeIn,'%Y-%m-%dT%T'),
     'outtime',DATE_FORMAT(TimeOut,'%Y-%m-%dT%T'),
     'comment',Error_Trip.Comment
    ),
     CONCAT(
       '{', JSON_QUOTE('rowers'),': [',
       GROUP_CONCAT(JSON_OBJECT(
           'name',CONCAT(Member.FirstName,' ',Member.LastName),
            'id',Member.MemberID) ORDER BY Seat),
   ']}')
   ) AS json
       FROM Error_Trip
            LEFT JOIN Error_TripMember on Error_Trip.id=Error_TripMember.ErrorTripID
            LEFT JOIN Member on member_id=Member.id
            LEFT JOIN Boat on Boat.id=BoatID
            LEFT JOIN TripType ON TripType.id=Error_Trip.TripTypeID
       WHERE Fixed=0
       GROUP BY Error_Trip.id, TripType.Name
)
  UNION 
    (SELECT Trip.id as Trip, NULL as error_id, JSON_MERGE(
    JSON_OBJECT(
      'reason','',
      'id', NULL,
      'Trip', Trip.id,
      'Reporter', NULL,
      'DeleteTrip',  NULL,
      'boat',  Boat.Name,
      'triptype', TripType.Name,
      'destination', Trip.Destination,
      'created', Trip.CreatedDate,
      'distance',  Trip.Meter,
      'intime',  DATE_FORMAT(InTime,'%Y-%m-%dT%T'),
      'outtime', DATE_FORMAT(OutTime,'%Y-%m-%dT%T'),
      'comment',Trip.Comment
      ),
     CONCAT(
       '{', JSON_QUOTE('rowers'),': [',
       GROUP_CONCAT(JSON_OBJECT(
           'name',CONCAT(Member.FirstName,' ',Member.LastName),
            'id',Member.MemberID) ORDER BY Seat),
   ']}')
   ) AS json
      FROM Boat,
           Trip
             LEFT JOIN TripMember on Trip.id=TripMember.TripID
             LEFT JOIN Member on member_id=Member.id,
           Error_Trip,
           TripType 
     WHERE Boat.id=Trip.BoatID
       AND Error_Trip.Trip=Trip.id
       AND TripType.id=Trip.TripTypeID
       AND Fixed=0
     GROUP BY Error_Trip.id,TripType.Name
    ) ORDER BY Trip,error_id" ;

 
if ($sqldebug) echo $sql."\n\n";

if ($stmt = $rodb->prepare($sql)) {
     $stmt->execute() or die("Error in etrips query: " . mysqli_error($rodb));
     $result= $stmt->get_result() or die("Error in etrips query: " . mysqli_error($rodb));
} else {
    $error=mysqli_error($rodb);
    error_log($error);
}

echo '[';
$first=1;
while ($row = $result->fetch_assoc()) {
    if ($first) $first=0; else echo ",\n";	  
    echo $row['json'];
}
echo ']';
$rodb->close();
?> 
