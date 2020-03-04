<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="instruktorstat.csv"');

$s="SELECT YEAR(NOW())-1 as sason, CONCAT(FirstName,' ',LastName) as Instruktor, MemberId as medlemsnummer, COUNT('x') AS instruktioner,
     GROUP_CONCAT(DISTINCT BoatType.Name SEPARATOR '/') as boattypes
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
$output='csv';
process($result,$output,"instruktørstatistik",array("season","instruktør","medlemsnr","ture","bådtyper"));
