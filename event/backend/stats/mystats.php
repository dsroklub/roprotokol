<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");

$q="none";
if (isset($_GET["q"])) {
    $q=$_GET["q"];
}

switch ($q) {
case "rights":
    $s="SELECT showname as member_right,argument as arg,DATE_FORMAT(acquired,'%Y-%m-%dT%T') as acquired
    FROM MemberRights, Member,MemberRightType
    WHERE Member.MemberID=? AND Member.id=MemberRights.member_id AND member_right=MemberRight AND NOT (MemberRightType.arg <> MemberRights.argument)
    ORDER BY acquired";
    break;
case "work":
    $s="SELECT DATE_FORMAT(start_time,'%Y-%m-%dT%T') as start_time,DATE_FORMAT(end_time,'%Y-%m-%dT%T') as end_time,hours,task,boat,worklog.created,work
    FROM worklog, Member
    WHERE Member.MemberID=? AND Member.id=worklog.member_id
    ORDER BY start_time";
    break;
case "worker":
    $s="SELECT DATE_FORMAT(start_time,'%Y-%m-%dT%T') as start_time,DATE_FORMAT(end_time,'%Y-%m-%dT%T') as end_time,hours,task,boat,worklog.created,work
    FROM worklog, Member
    WHERE Member.MemberID=? AND Member.id=worklog.member_id
    ORDER BY start_time";
    break;
case "mates":
    $s="SELECT CONCAT(them.FirstName,' ',them.LastName) as mate, SUM(Meter) as dist
    FROM Member me, Member them,Trip,TripMember tm, TripMember ttm
    WHERE me.MemberID=? AND tm.TripID=Trip.id AND tm.member_id=me.id AND them.id=ttm.member_id and ttm.TripID=Trip.id AND me.id!=them.id
    GROUP By mate
    ORDER BY dist DESC";
    break;
case "boats":
    $s="SELECT Boat.Name as boatname, SUM(Meter) as dist
    FROM Member me, Boat,Trip,TripMember tm
    WHERE me.MemberID=? AND tm.member_id=me.id AND Trip.id=tm.TripID AND Trip.BoatID=Boat.id
    GROUP By Boat.id
    ORDER BY dist DESC";
    break;
case "destinations":
    $s="SELECT Trip.Destination AS destination, COUNT(Trip.id) as numtrips,SUM(Trip.Meter) as distance
    FROM Member me,Trip,TripMember tm
    WHERE me.MemberID=? AND tm.member_id=me.id AND Trip.id=tm.TripID
    GROUP By Trip.Destination
    ORDER BY numtrips DESC";
    break;
case "triptypes":
    $s="SELECT TripType.Name AS triptype, COUNT(Trip.id) as numtrips
    FROM Member me,Trip,TripMember tm,TripType
    WHERE me.MemberID=? AND tm.member_id=me.id AND Trip.id=tm.TripID AND TripType.id=Trip.TripTypeID
    GROUP By TripType.id
    ORDER BY numtrips DESC
    LIMIT 20";
    break;
default:
    $res=["status" => "error", "error"=>"invalid query' .$q"];
    echo json_encode($res);
    exit(0);
}

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mystats $q");
$stmt->bind_param("s",$cuser);
$stmt->execute() ||  dbErr($rodb,$res,"mystats Exe $q");
$result= $stmt->get_result();
output_rows($result);
$stmt->close();
$rodb->close();
