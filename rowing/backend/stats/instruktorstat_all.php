<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");

$s="SELECT CONCAT(FirstName,' ',LastName) as instruktør,MemberId as medlemsnummer, YEAR(OutTime) as sæson, COUNT('x') AS instruktioner,
     GROUP_CONCAT(DISTINCT BoatType.Name SEPARATOR '/') as bådtyper
FROM Trip,TripMember, Member, TripType,Boat,BoatType
WHERE
  Boat.id=Trip.BoatID AND
  Boat.boat_type=BoatType.Name AND
  BoatType.Seatcount >=3 AND
  Trip.id=TripMember.TripID AND
  TripType.id=Trip.TripTypeID AND
  Member.id=TripMember.member_id AND YEAR(OutTime)>YEAR(NOW())-10 AND
  Member.id IN (SELECT member_id FROM MemberRights WHERE MemberRights.MemberRight='instructor' AND MemberRights.Acquired < Trip.OutTime) AND
  TripType.Name='Instruktion'
Group By Member.id,YEAR(OutTime)
ORDER BY FirstName,LastName,sæson desc
";
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in instruktorstat query: " );
$output='xlsx';
process($result,$output,"10års_instruktørstatistik","_auto");
