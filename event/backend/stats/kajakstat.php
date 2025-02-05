<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right(["admin"=>[null]]);
$s = 'SELECT YEAR(Trip.OutTime) as aar, TripType.name as turtype,CONCAT(FirstName," ",LastName) as navn, FORMAT(SUM(Meter/1000),1) as km
 FROM Trip,Member,TripType,Boat,TripMember,BoatType
 WHERE BoatType.Category=1 AND BoatType.Name=Boat.boat_type AND Member.id=TripMember.member_id AND TripMember.TripID=Trip.id AND Boat.id=Trip.BoatID AND TripType.id=TripTypeID
 GROUP by YEAR(Trip.OutTime), Member.id,turtype
 ORDER by aar,turtype,navn;
';
$result=$rodb->query($s) or dbErr($rodb,$res,"10aktive");
process($result,"xlsx","kajakstat","_auto");
