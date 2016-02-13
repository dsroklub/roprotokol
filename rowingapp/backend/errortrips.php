<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
  
$sql=
    " (SELECT Error_Trip.id as id, Error_Trip.Trip, Error_Trip.Reporter, DeleteTrip,Boat.Name AS boat, TripType.Name as triptype, Error_Trip.Destination as destination, Error_Trip.CreatedDate as created, Distance as distance, TimeIn as intime, TimeOut as outtime,GROUP_CONCAT(CONCAT(Member.FirstName,' ',Member.LastName),':§§:',Member.MemberID SEPARATOR '££') as rowers " .
    " FROM Error_Trip LEFT JOIN Error_TripMember on Error_Trip.id=Error_TripMember.TripID LEFT JOIN Member on member_id=Member.id LEFT JOIN Boat on Boat.id=BoatID, TripType ".
    " WHERE TripType.id=Error_Trip.TripTypeID OR Error_Trip.TripTypeID IS NULL GROUP BY Error_Trip.id".
    ")\n UNION " .
    " (SELECT NULL as id, Trip.id as Trip, NULL as Reporter, NULL as DeleteTrip,Boat.Name AS boat, TripType.Name as triptype, Trip.Destination as destination, Trip.CreatedDate as created, Meter as distance, InTime as intime, OutTime as outtime,GROUP_CONCAT(CONCAT(Member.FirstName,' ',Member.LastName),':§§:',Member.MemberID SEPARATOR '££') as rowers " .
    " FROM Boat, Trip LEFT JOIN TripMember on Trip.id=TripMember.TripID LEFT JOIN Member on member_id=Member.id,Error_Trip,  TripType ".
    " WHERE Boat.id=Trip.BoatID AND  Error_Trip.Trip=Trip.id AND TripType.id=Trip.TripTypeID GROUP BY Trip.id ".
    ") ORDER BY Trip,id" ;


 
#echo $sql."\n\n";
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
    if ($first) $first=0; else echo ',';	  
    $row['rowers']=multifield($row['rowers']);
    echo json_encode($row,JSON_FORCE_OBJECT);
}
echo ']';
$rodb->close();
?> 
