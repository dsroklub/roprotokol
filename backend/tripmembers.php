<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
if (isset($_GET["trip"])) {
    $trip=$_GET["trip"];
} else {
    echo "please set trip";
    exit(1);
}

$sql="SELECT id,BoatID,OutTime, InTime,ExpectedIn,Destination,Meter, TripTypeID,DESTID,Seat,member_id,MemberName FROM Trip,TripMember WHERE TripID=Trip.id AND Trip.id=? ORDER BY OutTime DESC";

//echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("i", $trip);
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
