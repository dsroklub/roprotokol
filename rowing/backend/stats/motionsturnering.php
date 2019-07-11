<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$res=$rodb->query("SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,3)) AS distance,COUNT(DISTINCT Member.id) as aktive_roere,FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,3) as km_per_aktiv_roer,MONTHNAME(NOW() - INTERVAL 1 MONTH) AS tilogmed
FROM 
    season,Trip,TripMember,Member,Boat,BoatType
WHERE 
  season.season=YEAR(NOW()) AND
Trip.OutTime > season.summer_start AND
MONTH(Trip.OutTime) < MONTH(NOW()) AND
Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
BoatType.Category=2 AND
STRCMP(MemberID,\"=\") <0 AND
TripMember.TripID=Trip.id AND Member.id=TripMember.member_id");
echo "<h1>Motionsturneringsstatistik</h1>\n";
process($res,"text",null,["km","aktive roere","gennemsnit","til og med"]);

echo "<h2>Top sommerroere</h2>";
$top=$rodb->query('
SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,3)) AS distance, CONCAT(FirstName," ",LastName) as roer ,MONTHNAME(NOW() - INTERVAL 1 MONTH) AS tilogmed
FROM 
    season,Trip,TripMember,Member,Boat,BoatType
WHERE 
  season.season=YEAR(NOW()) AND
Trip.OutTime > season.summer_start AND
MONTH(Trip.OutTime) < MONTH(NOW()) AND
Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
BoatType.Category=2 AND
STRCMP(MemberID,"=") <0 AND
TripMember.TripID=Trip.id AND Member.id=TripMember.member_id
GROUP BY Member.id
ORDER BY distance DESC LIMIT 3
'
);
process($top,"text",null,["km","roer","til og med"]);

$rodb->close();

