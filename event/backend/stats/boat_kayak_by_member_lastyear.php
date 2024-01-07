<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"],"gym"=>["admin"]]);
$s="
WITH mk as
(SELECT Member.MemberID, ROUND(SUM(Meter/1000),1) as distance, IF(rights_subtype='kayak','kajak','roning') as bc
FROM Member,Trip,TripMember,Boat, BoatType
WHERE TripMember.member_id=Member.id AND TripMember.TripID=Trip.id AND YEAR(Trip.OutTime)=YEAR(NOW())-1 AND Boat.id=Trip.BoatID AND BoatType.Name=Boat.boat_type
GROUP BY Member.MemberID,bc)
SELECT Member.MemberID as medlemsnummer, CONCAT(FirstName,' ',LastName) as navn, IFNULL(mkro.distance,0) as roning, IFNULL(mkkajak.distance,0) as kajak, ROUND(IFNULL(mkkajak.distance,0)*100/(IFNULL(mkkajak.distance,0)+IFNULL(mkro.distance,0)),1) as pct_kajak
  FROM Member LEFT JOIN mk mkro ON mkro.MemberID=Member.MemberID AND mkro.bc='roning' LEFT JOIN  mk mkkajak ON mkkajak.MemberID=Member.MemberID AND mkkajak.bc='kajak'
  HAVING roning>0 OR kajak>0
  ORDER BY Member.id
";
$result=$rodb->query($s) or dbErr($rodb,$res,"ro kajak member stat");
$output='xlsx';
if ($_GET["format"]=="json") $output='json';
if ($_GET["format"]=="csv") $output='csv';
process($result,$output,"ro,kajak pr medlem sidste år","_auto");
$rodb->close();
