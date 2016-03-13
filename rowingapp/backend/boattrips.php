<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
if (isset($_GET["boat"])) {
    $boat=$_GET["boat"];
} else {
    echo "please set boat";
    exit(1);
}
  
$sql="SELECT Trip.id, Boat.Name AS boat, Boat.id as boat_id, TripTypeID as triptype_id, Trip.Destination as destination, DATE_FORMAT(Trip.CreatedDate,'%Y-%m-%dT%T') as created, Meter as distance, DATE_FORMAT(InTime,'%Y-%m-%dT%T') as intime, DATE_FORMAT(OutTime,'%Y-%m-%dT%T') as outtime " .
    " FROM Boat,Trip WHERE Boat.id=? AND Boat.id = Trip.BoatID AND Trip.OutTime>=? ORDER BY Trip.id DESC";

# echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("is", $boat,$season);
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
