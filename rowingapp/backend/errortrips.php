<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
  
$sql=
    " (SELECT ReasonForCorrection as reason,
              Error_Trip.id as id,
              Error_Trip.Trip,
              Error_Trip.Reporter,
              DeleteTrip,
              Boat.Name AS boat,
              TripType.Name as triptype,
              Error_Trip.Destination as destination,
              Error_Trip.CreatedDate as created,
              Distance as distance,
              TimeIn as intime,
              TimeOut as outtime,
              GROUP_CONCAT(CONCAT(Member.FirstName,' ',Member.LastName),':§§:',Member.MemberID ORDER BY Seat SEPARATOR '££' ) as rowers
       FROM Error_Trip
            LEFT JOIN Error_TripMember on Error_Trip.id=Error_TripMember.ErrorTripID
            LEFT JOIN Member on member_id=Member.id
            LEFT JOIN Boat on Boat.id=BoatID
            LEFT JOIN TripType ON TripType.id=Error_Trip.TripTypeID
       WHERE Fixed=0
       GROUP BY Error_Trip.id, TripType.Name) 
  UNION 
    (SELECT '' as reason,
            NULL as id,
            Trip.id as Trip,
            NULL as Reporter,
            NULL as DeleteTrip,
            Boat.Name AS boat,
            TripType.Name as triptype,
            Trip.Destination as destination,
            Trip.CreatedDate as created,
            Meter as distance,
            InTime as intime,
            OutTime as outtime,
            GROUP_CONCAT(CONCAT(Member.FirstName,' ',Member.LastName),':§§:',Member.MemberID ORDER BY Seat SEPARATOR '££') as rowers 
      FROM Boat,
           Trip
             LEFT JOIN TripMember on Trip.id=TripMember.TripID
             LEFT JOIN Member on member_id=Member.id,
           Error_Trip,
           TripType 
     WHERE Boat.id=Trip.BoatID
       AND Error_Trip.Trip=Trip.id
       AND TripType.id=Trip.TripTypeID
     GROUP BY Trip.id,TripType.Name
    ) ORDER BY Trip,id" ;

 
if ($sqldebug) echo $sql."\n\n";

if ($stmt = $rodb->prepare($sql)) {
    error_log("now exe");
     $stmt->execute() or die("Error in etrips query: " . mysqli_error($rodb));
     $result= $stmt->get_result() or die("Error in etrips query: " . mysqli_error($rodb));
} else {
    $error=mysqli_error($rodb);
    error_log($error);
}

echo '[';
$first=1;
while ($row = $result->fetch_assoc()) {
    if ($first) $first=0; else echo ',';	  
    $row['rowers']=multifield($row['rowers']);
    echo json_encode($row,JSON_FORCE_OBJECT);
}
echo ']';
$rodb->close();
?> 
