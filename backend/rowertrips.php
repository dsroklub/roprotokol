<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
if (isset($_GET["member"])) {
    $member=$_GET["member"];
} else {
    echo "please set member";
    exit(1);
}

$sql="SELECT Trip.TripID as id, Boat.Name AS boat, Trip.Destination as destination, Trip.CreatedDate as created, Meter as distance, Member.MemberID, CONCAT(Firstname,' ',Lastname) AS Name " .
    " FROM Boat RIGHT JOIN (Member INNER JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON Member.id = TripMember.member_id) ON Boat.id = Trip.BoatID " .
    " WHERE Member.MemberID=? AND Trip.Season=? ORDER BY Trip.TripID DESC;";

//echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("si", $member,$season);
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
