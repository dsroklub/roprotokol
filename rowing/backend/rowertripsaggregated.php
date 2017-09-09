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

$season=date('Y',time());
if (isset($_GET["season"])) {
    $season=$_GET["season"];
}

$sql=
    "SELECT TripType.Name AS triptype, Count(Trip.id) AS trip_count, Sum(Meter) AS distance, Sum(Meter)/Count(Trip.id) as average " .
    " FROM Trip, TripMember,TripType,Member " .
    " WHERE  Member.id=TripMember.member_id AND TripMember.TripID=Trip.id AND Trip.TripTypeID = TripType.id AND (? OR YEAR(Trip.OutTime)=?) " .
    " AND Member.MemberID=? " .
    " GROUP BY TripType.Name";
# echo $sql;
if ($stmt = $rodb->prepare($sql)) {
    $qseason=(($season=="all")?"0":$season);
    $useseason=($season=="all")?1:0;
    $stmt->bind_param("iss", $useseason,$qseason,$member);
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
