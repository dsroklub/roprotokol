<?php
include("../inc/common.php");
include("../inc/utils.php");

$year=date('Y');
$month=date('m');

$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);

if (isset($_GET["year"])) {
    $year=(int)$_GET["year"];
}
$tilogmed="MONTHNAME(NOW() - INTERVAL 1 MONTH)";
if (isset($_GET["month"])) {
    $month=(int)$_GET["month"];
    $dt = DateTime::createFromFormat('!m', $month-1);
    $tilogmed=$dt->format('F');
}
echo "<h1>Motionsturneringsstatistik $year</h1>\n";

// echo "y=$year,m=$month";


$res=$rodb->query("
SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,2)) AS distance,
  COUNT(DISTINCT Member.id) as aktive_roere,
  FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,2) as km_per_aktiv_roer,
  '$tilogmed' AS tilogmed
FROM
    Trip,TripMember,Member,Boat,BoatType,season
WHERE
  season.season=$year AND
  Date(Trip.OutTime) >= season.summer_start AND
  YEAR(Trip.OutTime)=$year AND
  MONTH(Trip.OutTime) < $month AND
  Boat.id=Trip.BoatID AND
  Boat.boat_type=BoatType.Name AND
  BoatType.Category=2 AND
  BoatType.boat_class !='motor' AND
  Member.MemberID NOT LIKE 'g%' AND
  Member.MemberID NOT LIKE 'N%' AND
  TripMember.TripID=Trip.id AND
  Member.id=TripMember.member_id"
) or dbErr($rodb,$res,"motion");

process($res,"text",null,["km robåd","aktive roere","gennemsnit","til og med"]);


$res=$rodb->query("
SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,2)) AS distance,
  COUNT(DISTINCT Member.id) as aktive_roere,
  FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,2) as km_per_aktiv_roer,
  '$tilogmed' AS tilogmed
FROM
    Trip,TripMember,Member,Boat,BoatType,season
WHERE
  season.season=$year AND
  Date(Trip.OutTime) >= season.summer_start AND
  YEAR(Trip.OutTime)=$year AND
  MONTH(Trip.OutTime) < $month AND
  Boat.id=Trip.BoatID AND
  Boat.boat_type=BoatType.Name AND
  (BoatType.Category=2 OR BoatType.Category=1) AND
  BoatType.boat_class !='motor' AND
  Member.MemberID NOT LIKE 'g%' AND
  Member.MemberID NOT LIKE 'N%' AND
  TripMember.TripID=Trip.id AND
  Member.id=TripMember.member_id"
) or dbErr($rodb,$res,"motion");

process($res,"text",null,["km robåd+kajak","aktive roere","gennemsnit","til og med"]);


echo "<h2>Top sommerroere</h2>";
$top=$rodb->query("
SELECT CAST(SUM(Meter)/1000 AS DECIMAL(10,3)) AS distance, CONCAT(FirstName,' ',LastName) as roer,'$tilogmed' AS tilogmed
FROM
    Trip,TripMember,Member,Boat,BoatType,season
WHERE
  season.season=$year AND
  Date(Trip.OutTime) >= season.summer_start AND
  YEAR(Trip.OutTime)=$year AND
  MONTH(Trip.OutTime) > 3 AND
  MONTH(Trip.OutTime) < $month AND
  Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
  BoatType.Category=2 AND
  BoatType.boat_class !='motor' AND
  TripMember.TripID=Trip.id AND Member.id=TripMember.member_id
GROUP BY Member.id
ORDER BY distance DESC
LIMIT 10
"
);
process($top,"text",null,["km","roer","til og med"]);


$s="
SELECT m.4 AS maaned,
       FORMAT(SUM(Meter)/1000 ,2) AS distance,
       COUNT(DISTINCT Member.id) as aktive_roere,
       FORMAT(SUM(Meter)/COUNT(DISTINCT Member.id)/1000,2) as km_per_aktiv_roer
FROM
    Trip,TripMember,Member,Boat,BoatType,season,(VALUES (4),(5),(6),(7),(8),(9),(10)) as m
WHERE
  season.season=$year AND
  Date(Trip.OutTime) >= season.summer_start AND
  YEAR(Trip.OutTime)=$year AND
  MONTH(Trip.OutTime) <= m.4 AND
  Boat.id=Trip.BoatID AND
  Boat.boat_type=BoatType.Name AND
  BoatType.Category=2 AND
  BoatType.boat_class !='motor' AND
  Member.MemberID NOT LIKE 'g%' AND
  Member.MemberID NOT LIKE 'N%' AND
  TripMember.TripID=Trip.id AND
  Member.id=TripMember.member_id
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
    Trip,TripMember,Member,Boat,BoatType,season,(VALUES (4),(5),(6),(7),(8),(9),(10)) as m
WHERE
  season.season=$year AND
  Date(Trip.OutTime) >= season.summer_start AND
  Member.MemberID NOT LIKE 'g%' AND
  Member.MemberID NOT LIKE 'N%' AND
  YEAR(Trip.OutTime)=$year AND
  MONTH(Trip.OutTime) <= m.4 AND
  Boat.id=Trip.BoatID AND Boat.boat_type=BoatType.Name AND
  (BoatType.Category=2 OR BoatType.Category=1) AND
  BoatType.boat_class !='motor' AND
  BoatType.Name!='Polokajak' AND
  TripMember.TripID=Trip.id AND Member.id=TripMember.member_id
  GROUP BY m.4
  HAVING maaned>3
";
$res=$rodb->query($s) or die("Error ".$rodb->error);
echo "<h1>Statistik  pr m&aring;ned, rob&aring;de,kajak</h1>\n";
process($res,"text",null,["m&aring;ned","km","aktive roere","gennemsnit"]);



$rodb->close();
