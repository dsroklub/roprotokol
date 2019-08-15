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

$sql="SELECT Trip.id, Boat.Name AS boat, Boat.id as boat_id, TripTypeID as triptype_id,
    Trip.Destination as destination, DATE_FORMAT(Trip.CreatedDate,'%Y-%m-%d %T') as created,
    Meter as distance, DATE_FORMAT(InTime,'%Y-%m-%d %T') as intime, DATE_FORMAT(OutTime,'%Y-%m-%d %T') as outtime,
    DATE_FORMAT(ExpectedIn,'%Y-%m-%d %T') as expectedin, Comment as comment, TripType.name as triptype
    FROM Boat,Trip,TripType
    WHERE Boat.id=? AND Boat.id = Trip.BoatID AND Trip.OutTime>=? AND TripType.id=Trip.TripTypeID
    ORDER BY Trip.id DESC";

if ($sqldebug) {
 echo $sql;
 echo "\n";
}
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("is", $boat,$season);
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
