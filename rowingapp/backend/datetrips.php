<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
if (isset($_GET["tripdate"])) {
    $tripdate=$_GET["tripdate"];
} else {
    echo "please set tripdate";
    exit(1);
}
  
$sql="SELECT Trip.id, Boat.Name AS boat, Boat.id as boat_id, TripTypeID as triptype_id, TripType.name as triptype,
    Trip.Destination as destination, DATE_FORMAT(Trip.CreatedDate,'%Y-%m-%dT%T') as created, Meter as distance, 
    DATE_FORMAT(InTime,'%Y-%m-%d %T') as intime, DATE_FORMAT(OutTime,'%Y-%m-%dT%T') as outtime, 
	DATE_FORMAT(ExpectedIn,'%Y-%m-%dT%T') as expectedin, Comment as comment 
    FROM Boat,Trip,TripType  
    WHERE Date(Trip.OutTime)=? AND Boat.id = Trip.BoatID  AND TripType.id=Trip.TripTypeID
    ORDER BY Trip.id DESC";

if ($sqldebug) {
 echo $sql;
 echo "\n";
}
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("s", $tripdate);
     $stmt->execute();
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
} else {
	error_log("Prepare failed: " .$rodb->error);
	$result = [];
}
echo '[';
$first=1;
while ($row = $result->fetch_assoc()) {
    if ($first) $first=0; else echo ',';	  
    echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
