<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");

$s="SELECT CONCAT(FirstName,' ',LastName) as instruktør,MemberId as medlemsnummer, YEAR(NOW())-1 as sæson, COUNT('x') AS instruktioner,
     GROUP_CONCAT(DISTINCT BoatType.Name SEPARATOR '/') as bådtyper
FROM Trip,TripMember, Member, TripType,MemberRights,Boat,BoatType
WHERE
  Boat.id=Trip.BoatID AND
  Boat.boat_type=BoatType.Name AND
  BoatType.Seatcount >=3 AND
  Trip.id=TripMember.TripID AND
  TripType.id=Trip.TripTypeID AND
  Member.id=TripMember.member_id AND YEAR(OutTime)=YEAR(NOW())-1 AND
  MemberRights.member_id=Member.id AND MemberRights.MemberRight='instructor' AND
  TripType.Name='Instruktion'
Group By Member.id
ORDER BY instruktioner desc,FirstName,LastName
";
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in instruktorstat query: " );
$output='xlsx';
process($result,$output,"instruktørstatistik","_auto");
