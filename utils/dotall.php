#!/usr/bin/php
<?php
define( 'ROOT_DIR', dirname(__FILE__) );
$config = parse_ini_file(ROOT_DIR . '/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

echo 'graph "DSR roture" {';
echo
'edge [
color = "blue"
]
';        

echo 'node [
shape = "ellipse"
style="filled"
color = "gold"
fillcolor="gold"

	]
';



$result = $rodb->query('
SELECT CONCAT (FirstName," ", MemberId) as rower,MemberId as mid
FROM  Member WHERE id IN (
SELECT member_id FROM Trip,TripMember
WHERE YEAR(OutTime)=YEAR(NOW()) AND MONTH(OutTime)=MONTH(NOW()) AND Trip.id=TripMember.TripID) AND
NOT MemberID LIKE "g%"
') or exit("dot gen trips\n". $rodb->error);


foreach ($result as $tr) {
    echo "r".$tr["mid"] . ' [label="'. explode(' ',$tr["rower"])[0] .'"];'."\n";
}

$result = $rodb->query('
SELECT m1.MemberId as m1, m2.MemberId as m2, Sum(Trip.Meter) as distance
FROM Member m1, Member m2,Trip,TripMember tm1,TripMember tm2
WHERE 
tm1.TripID=Trip.id AND tm2.TripID=Trip.id AND
m1.id=tm1.member_id AND m2.id=tm2.member_id AND
m1.id<m2.id AND
YEAR(OutTime)=YEAR(NOW()) AND MONTH(OutTime)=MONTH(NOW()) 
GROUP BY Trip.id,m1.id,m2.id
') or exit("dot gen ". $rodb->error);
foreach ($result as $rt) {
    $rower1=$rt["m1"];
    $rower2=$rt["m2"];
    $len=0.1+10000/(400 +$rt["distance"]);
    echo " r$rower1 -- r$rower2 [len = $len] [label=\"". round($rt["distance"]/1000)."\"]\n";
}
echo '}';
