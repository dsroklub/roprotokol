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

$sql="SELECT Seat as seat,MemberID as id, CONCAT(FirstName,' ',LastName) as name 
FROM Trip,TripMember,Member 
WHERE TripID=Trip.id AND Member.id=member_id AND Trip.id=? 
GROUP BY member_id,seat,OutTime 
ORDER BY OutTime";

if ($sqldebug) echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $stmt->bind_param("i", $trip);
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
