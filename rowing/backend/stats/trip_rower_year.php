<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

if (isset($_GET["allboats"])) {
    $qboats="";
} else {
    $qboats=" AND Category=2 ";
}

if (isset($_GET["km"])) {
    $km="/1000";
} else {
    $km=" ";
}

$s="SELECT YEAR(Trip.OutTime) as year,TripType.tripstat_name as name,COUNT(DISTINCT Trip.id),COUNT(Member.id),COUNT(DISTINCT Member.id)
    FROM Trip,TripType,Boat,BoatType,TripMember,Member
    WHERE Trip.TripTypeID=TripType.id AND Trip.BoatID=Boat.id $qboats AND Boat.BoatType=BoatType.id 
    AND Trip.OutTime IS NOT NULL
    AND TripMember.TripID=Trip.id
    AND TripMember.member_id=Member.id
    GROUP BY TripType.name, year,TripType.tripstat_name
    ORDER BY year,TripType.tripstat_name";

if ($sqldebug) {
    echo $s;
}

if ($stmt = $rodb->prepare($s)) {
     $stmt->execute();
     $result= $stmt->get_result();
     process($result,$output,"roerstatistik",array("season","turtype","ture","roerture","unikke roere"));
     $stmt->close();
 }
$rodb->close();
