<?php
set_include_path(get_include_path().':..');
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
include("inc/common.php");

$s="
SELECT FORMAT(SUM(Meter)/1000 ,2) AS distance,COUNT(DISTINCT Member.id) as aktive_roere,FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,3) as km_per_aktiv_roer,MONTHNAME(NOW() - INTERVAL 1 MONTH) AS tilogmed
FROM 
    season,Trip,TripMember,Member,Boat,BoatType
WHERE 
  season.season=YEAR(NOW()) AND
Trip.OutTime > season.summer_start AND
MONTH(Trip.OutTime) < MONTH(NOW()) AND
Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
BoatType.Category=2 AND
STRCMP(MemberID,\"=\") <0 AND
TripMember.TripID=Trip.id AND Member.id=TripMember.member_id";
$res=$rodb->query($s);
echo "<h1>Motionsturneringsstatistik</h1>\n";
process($res,"text",null,["km","aktive roere","gennemsnit","til og med"]);

$rodb->close();

