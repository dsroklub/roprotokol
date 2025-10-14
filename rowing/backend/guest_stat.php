<?php
include("inc/common.php");
$gs=[];
$guest=$rodb->query(
    "SELECT Member.MemberID as guest, IFNULL(SUM(Trip.Meter)/1000,0) as km
     FROM Member LEFT JOIN TripMember ON Member.id=TripMember.member_id LEFT JOIN Trip ON TripMember.TripID=Trip.id AND YEAR(Trip.OutTime)=YEAR(NOW())
     WHERE Member.membertype='associeret'
     GROUP by Member.MemberID
 ") or dbErr($rodb,$res,"guest stat");
while ($gr = $guest->fetch_assoc()) {
    $gs[$gr["guest"]]=(float)$gr["km"];
}
echo json_encode($gs,JSON_PRETTY_PRINT);
$rodb->close();
