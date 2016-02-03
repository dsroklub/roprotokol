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
  
$sql="SELECT Trip.id, Boat.Name AS boat, Boat.id as boat_id, TripTypeID as triptype_id, Trip.Destination as destination, Trip.CreatedDate as created, Meter as distance, InTime as intime, OutTime as outtime FROM Boat,Trip 
WHERE Date(Trip.OutTime)=? AND Boat.id = Trip.BoatID ORDER BY Trip.id DESC";

//echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("s", $tripdate);
     $stmt->execute();
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
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
