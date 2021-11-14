<?php
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$s="SELECT GROUP_CONCAT(CONCAT(FirstName,' ',LastName,' (',MemberID,')')) AS roere, TripType.Name as turtype,IFNULL(starting_place,'') as fra, Destination as til, ROUND(Meter/1000,2) as km, WEEK(OutTime) as uge,DATE_FORMAT(OutTime,'%Y-%m-%d %H:%i') as ud, DATE_FORMAT(InTime,'%Y-%m-%d %H:%i') as ind, Comment as kommentar, Boat.Name as båd, Boat.boat_type as bådtype
FROM Member,Trip,TripMember,TripType,Boat
WHERE
  TripType.id=Trip.TripTypeID AND
  YEAR(Trip.OutTime)=YEAR(NOW()) AND
  TripMember.member_id=Member.id AND
  Trip.id=TripMember.TripID AND
  Trip.BoatID=Boat.id AND
  Boat.boat_type <> 'Motorbåde' AND
  (Boat.boat_type LIKE 'Coastal%' OR TripType.Name='Coastalkaproning')
  GROUP BY Trip.id
  ORDER BY ud";
$result=$rodb->query($s) or dbErr($rodb,$res,"coastal stat");
$output='xlsx';
if ($_GET["format"]=="json") $output='json';
if ($_GET["format"]=="csv") $output='csv';
process($result,$output,"Coastal i år","_auto");
$rodb->close();
