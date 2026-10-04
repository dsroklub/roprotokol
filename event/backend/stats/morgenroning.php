<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$s='
SELECT MemberID as medlemsnummer,CONCAT(FirstName," ",LastName) as navn, ROUND(Sum(Meter)/1000,1) AS Distance, COUNT("x") AS gange
FROM Trip,TripMember, Member, TripType 
WHERE Trip.id=TripMember.TripID AND TripType.id=Trip.TripTypeID AND Member.id=TripMember.member_id AND YEAR(OutTime)=YEAR(NOW()) AND TripType.Name="Motionsroning" AND
((TIME(Trip.OutTime)>TIME("06:30") AND TIME(Trip.OutTime)<TIME("07:30")) OR
(TIME(Trip.OutTime)>TIME("06:30") AND TIME(Trip.OutTime)<TIME("09:00") AND (MONTH(Trip.OutTime) BETWEEN 11 AND 12  OR MONTH(Trip.OutTime) BETWEEN 1 AND 2 ))
)
AND
(starting_place="Nordhavn" or starting_place="DSR")
Group By Member.id ORDER BY gange DESC, Distance DESC,FirstName,LastName 
';
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in morgenroning query: " );
$output='xlsx';
process($result,$output,"morgenroning","_auto");
