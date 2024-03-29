<?php
include("inc/common.php");
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
    $format=$_GET["output"] ?? "csv";

    $s="SELECT OutTime as tid,Boat.name as båd,Trip.Meter/1000 as km ,Trip.Destination as destination
    FROM Trip,TripMember,Member,Boat
    WHERE TripMember.TripID=Trip.id AND Member.id=TripMember.member_id AND Member.MemberID=?
       AND Boat.id=Trip.BoatId
UNION
   SELECT start_time as tid, team as båd, 0 as km, '' as destination
   FROM team_participation, Member
   WHERE team_participation.member_id=Member.id AND Member.MemberId=?
ORDER BY tid DESC";
    if ($sqldebug) echo $s;
    $stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mytrips");
    $stmt->bind_param("ss",$cuser,$cuser) || dbErr($rodb,$res,"mytrips");
    $stmt->execute() || dbErr($rodb,$res,"mytrips exe");
    $result= $stmt->get_result();
    process($result,$output,"${cuser}_roture","_auto");
    $stmt->close();
} else {
    echo "No user";
}
$rodb->close();
