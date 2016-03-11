<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
$fromdate="2009-01-01";
if (isset($_GET["member"])) {
    $member=$_GET["member"];
} else {
    echo "please set member";
    exit(1);
}
# FIXME replace Trip.Season with outdate comparison
  
$sql="SELECT Trip.id, Boat.Name AS boat, Boat.id as boat_id, TripTypeID as triptype_id, Trip.Destination as destination, Trip.CreatedDate as created, Meter as distance, InTime as intime, OutTime as outtime  
FROM Boat RIGHT JOIN (Member INNER JOIN (Trip INNER JOIN TripMember ON Trip.id = TripMember.TripID) ON Member.id = TripMember.member_id) ON Boat.id = Trip.BoatID 
    WHERE Member.MemberID=? AND Trip.OutTime>=? ORDER BY Trip.id DESC;";

//echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("ss", $member,$fromdate);
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
