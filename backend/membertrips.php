<?php
include("inc/common.php");

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$memberid = -1;
if (isset($_GET["memberid"])) {
    $memberid=$_GET["memberid"];
}
        
$s="SELECT Trip.TripID as trip_id, Båd.Navn AS boat, Trip.Destination as destination, Trip.CreatedDate , Meter AS Triplength, Medlem.Medlemsnr as member_id, Fornavn & \" \" & Efternavn AS Navn ".
    " FROM Båd RIGHT JOIN (Medlem INNER JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON Medlem.MedlemID = TripMember.MemberID) ON Båd.BådID = Trip.BoatID ".
    "WHERE Medlem.Medlemsnr=? ORDER BY Trip.TripID DESC";

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
