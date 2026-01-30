<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$res=$rodb->query("SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,3)) AS distance,COUNT(DISTINCT Member.id) as aktive_roere,FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,3) as km_per_aktiv_roer,MONTHNAME(NOW() - INTERVAL 1 MONTH) AS tilogmed
FROM
    Trip,TripMember,Member,Boat,BoatType,MemberRights
WHERE
Member.id=MemberRights.member_id AND
MemberRight='rowright' AND
YEAR(Trip.OutTime)=YEAR(NOW()) AND
MONTH(Trip.OutTime) > 3 AND
MONTH(Trip.OutTime) < MONTH(NOW()) AND
Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
BoatType.Category=2 AND
TripMember.TripID=Trip.id AND Member.id=TripMember.member_id") or dbErr($rodb,$res,"motion");
echo "<h1>Motionsturneringsstatistik</h1>\n";
process($res,"text",null,["km","aktive roere","gennemsnit","til og med"]);
echo "<h2>Top sommerroere</h2>";
$top=$rodb->query('
SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,3)) AS distance, CONCAT(FirstName," ",LastName) as roer ,MONTHNAME(NOW() - INTERVAL 1 MONTH) AS tilogmed
FROM
    Trip,TripMember,Member,Boat,BoatType
WHERE
YEAR(Trip.OutTime)=YEAR(NOW()) AND
MONTH(Trip.OutTime) < MONTH(NOW()) AND
MONTH(Trip.OutTime)>3 AND
Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
BoatType.Category=2 AND
STRCMP(MemberID,"=") <0 AND
TripMember.TripID=Trip.id AND Member.id=TripMember.member_id
GROUP BY Member.id
ORDER BY distance DESC LIMIT 3
'
);
process($top,"text",null,["km","roer","til og med"]);


$s="
SELECT m.4 AS maaned,
       FORMAT(SUM(Meter)/1000 ,2) AS distance,
       COUNT(DISTINCT Member.id) as aktive_roere,
       FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,3) as km_per_aktiv_roer
FROM
    Trip,TripMember,Member,Boat,BoatType,MemberRights,season,(VALUES (4),(5),(6),(7),(8),(9),(10)) as m
WHERE
  season.season=YEAR(NOW()) AND
  Member.id=MemberRights.member_id AND
  MemberRight='rowright' AND
  YEAR(Trip.OutTime)=YEAR(NOW()) AND
  Date(Trip.OutTime) >= season.summer_start AND
  MONTH(Trip.OutTime) <= m.4 AND
  Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
  BoatType.Category=2 AND
  TripMember.TripID=Trip.id AND Member.id=TripMember.member_id
  GROUP BY m.4
  HAVING maaned>3
";
$res=$rodb->query($s) or die("Error ".$rodb->error);
echo "<h1>Statistik  pr m&aring;ned, rob&aring;de</h1>\n";
process($res,"text",null,["m&aring;ned","km","aktive roere","gennemsnit"]);

$s="
SELECT m.4 AS maaned,
       FORMAT(SUM(Meter)/1000 ,2) AS distance,
       COUNT(DISTINCT Member.id) as aktive_roere,
       FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,3) as km_per_aktiv_roer
FROM
    Trip,TripMember,Member,Boat,BoatType,MemberRights,season,(VALUES (4),(5),(6),(7),(8),(9),(10)) as m
WHERE
  season.season=YEAR(NOW()) AND
  Member.id=MemberRights.member_id AND
  MemberRight='rowright' AND
  YEAR(Trip.OutTime)=YEAR(NOW()) AND
  Date(Trip.OutTime) >= season.summer_start AND
  MONTH(Trip.OutTime) <= m.4 AND
  Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
  (BoatType.Category=2 OR BoatType.Category=1) AND
  BoatType.Name!='Polokajak' AND
  TripMember.TripID=Trip.id AND Member.id=TripMember.member_id
  GROUP BY m.4
  HAVING maaned>3
";
$res=$rodb->query($s) or die("Error ".$rodb->error);
echo "<h1>Statistik  pr m&aring;ned, rob&aring;de,kajak</h1>\n";
process($res,"text",null,["m&aring;ned","km","aktive roere","gennemsnit"]);



$rodb->close();
