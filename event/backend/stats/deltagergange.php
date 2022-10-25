<?php
include("../inc/common.php");
include("../inc/utils.php");
$s="
SELECT mg.år,mg.turtype, mg.gange as turtypegange, COUNT('g') as medlemmer FROM
(
SELECT Member.id,TripType.Name as turtype,count('x') as gange,YEAR(Trip.OutTime) as år
FROM Trip,Member,TripMember,TripType
WHERE TripMember.TripID=Trip.ID AND TripMember.member_id=Member.id AND Trip.TripTypeID=TripType.id AND  YEAR(Trip.OutTime)>YEAR(NOW())-11
GROUP BY år,Member.id,TripType.Name) as mg
GROUP by mg.år,mg.turtype,mg.gange
ORDER by år,turtype, turtypegange
";
$result=$rodb->query($s) or dbErr($rodb,$res,"deltagergange query: " );
$output='xlsx';
process($result,$output,"deltagergange","_auto");
