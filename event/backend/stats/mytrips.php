<?php
include("../../../rowing/backend/inc/common.php");
include("utils.php");
$format=$_GET["format"] ?? "json";
$fromdate="2009-01-01";
$sql="SELECT Trip.id, Boat.Name AS boat, Boat.id as boat_id, TripTypeID as triptype_id, TripType.name as triptype, Boat.boat_type,
    Trip.Destination as destination, DATE_FORMAT(Trip.CreatedDate,'%Y-%m-%dT%T') as created, Meter as distance,
     DATE_FORMAT(OutTime,'%Y-%m-%dT%T') as outtime,DATE_FORMAT(InTime,'%Y-%m-%dT%T') as intime,
    DATE_FORMAT(ExpectedIn,'%Y-%m-%dT%T') as expectedin, Comment as comment
    FROM Boat RIGHT JOIN (Member INNER JOIN (Trip JOIN TripMember ON Trip.id = TripMember.TripID JOIN TripType ON TripTypeID=TripType.id) ON Member.id = TripMember.member_id) ON Boat.id = Trip.BoatID
    WHERE Member.MemberID=? AND Trip.OutTime>=? ORDER BY Trip.id DESC;";
//echo $sql;
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"rower stat Q");
$stmt->bind_param("ss", $cuser,$fromdate) or dbErr($rodb,$res,"my rower bind");
$stmt->execute() ||  dbErr($rodb,$res,"rower stat QE");
$result= $stmt->get_result() or dbErr($rodb,$res,"Error in stat query");
process($result,$format,"mine roture $cuser","_auto");
$rodb->close();
