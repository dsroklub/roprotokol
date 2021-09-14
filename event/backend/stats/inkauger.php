<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$s="SELECT CONCAT(FirstName,' ',LastName) AS navn, MemberID AS medlemsnummer, COUNT('x') as ture, ROUND(SUM(Meter)/1000,2) as km, WEEK(OutTime) as uge, GROUP_CONCAT(DISTINCT TripType.Name) as turtyper
FROM Member,Trip,TripMember,TripType,Boat
WHERE
  TripType.id=Trip.TripTypeID AND
  YEAR(Trip.OutTime)=YEAR(NOW()) AND
  TripMember.member_id=Member.id AND
  Trip.id=TripMember.TripID AND
  Trip.BoatID=Boat.id AND
  Boat.boat_type <> 'Motorbåde' AND
  EXISTS (SELECT 'x' FROM Trip t, TripMember tm, TripType tt WHERE t.TripTypeID=tt.id AND tm.member_id=Member.id AND tm.TripID=t.id AND (tt.Name='Inriggerkaproning' OR tt.Name='Coastalkaproning') AND YEAR(t.OutTime)=YEAR(NOW()))
  GROUP BY MemberID,uge
  ORDER BY navn,uge";


$result=$rodb->query($s) or dbErr($rodb,$res,"inka stat");
$output='xlsx';
process($result,$output,"inka-uger","_auto");
$rodb->close();
