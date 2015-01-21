<?php
include("inc/common.php");
header('Content-type: application/json');

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$memberid = -1;
if (isset($_GET["memberid"])) {
    $memberid=$_GET["memberid"];
}
        
$s="SELECT Trip.TripID as trip_id, Boat.Name AS boat, Trip.Destination as destination, Trip.CreatedDate , Meter AS Triplength, Member.MemberID as member_id, FirstName & \" \" & LastName AS name ".
    " FROM Boat RIGHT JOIN (Member INNER JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON Member.id = TripMember.MemberID) ON Boat.id = Trip.BoatID ".
    "WHERE Member.MemberID=? ORDER BY Trip.TripID DESC";

// echo $s."\n<p>\n";
if ($stmt = $rodb->prepare($s)) {
     $stmt->bind_param('i', $memberid);
     $stmt->execute(); 
     $result= $stmt->get_result();
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
