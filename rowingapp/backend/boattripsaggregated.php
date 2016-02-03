<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
if (isset($_GET["boat"])) {
    $boat=$_GET["boat"];
} else {
    echo "please set if for boat";
    exit(1);
}

$sql=
    "SELECT TripType.Name AS triptype, Count(Trip.id) AS trip_count, Sum(Meter) AS distance, Sum(Meter)/Count(Trip.id) as average " .
    " FROM Trip, TripMember,TripType " .
    " WHERE  Trip.BoatID=? AND TripMember.TripID=Trip.id AND Trip.TripTypeID = TripType.id AND Trip.Season=? " .
    " GROUP BY TripType.Name";
// echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("ii", $season,$boat);
     $stmt->execute();
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
     echo '[';
     $first=1;
     while ($row = $result->fetch_assoc()) {
         if ($first) $first=0; else echo ',';	  
         echo json_encode($row);
     }
     echo ']';
}
$rodb->close();
?> 
