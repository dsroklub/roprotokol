<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right(["admin"=>null,"data"=>"stat"]);

$s='SELECT MemberID as medlemsnummer,CONCAT(FirstName," ",LastName) as navn, ROUND(Sum(Meter)/1000,1) AS Distance, COUNT("x") AS gange
FROM Trip,TripMember, Member, TripType
WHERE Trip.id=TripMember.TripID AND TripType.id=Trip.TripTypeID AND Member.id=TripMember.member_id AND YEAR(OutTime)=YEAR(NOW()) AND TripType.Name="Førsteårsroning" Group By Member.id ORDER BY FirstName,LastName
';
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in racerkaniner query: " );
$output='xlsx';
process($result,$output,"førsteårsroere","_auto");
