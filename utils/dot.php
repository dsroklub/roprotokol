#!/usr/bin/php
<?php
define( 'ROOT_DIR', dirname(__FILE__) );
$config = parse_ini_file(ROOT_DIR . '/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

echo 'graph "DSR roture" {';
        echo
'edge [
color = "black"
]
';        

echo 'node [
shape = "ellipse"
width = "0.400000"
height = "0.400000"
color = "black"
bgcolor = "yellow"
	]
';


$result = $rodb->query('
SELECT Boat.Name as boat,Trip.Meter as distance,TripType.Name as trip_type,Trip.id as tid
FROM  Boat,Trip,TripType
WHERE Boat.id=Trip.BoatID AND
YEAR(OutTime)=YEAR(NOW()) AND MONTH(OutTime)=MONTH(NOW()) AND
TripType.id=Trip.TripTypeID
ORDER BY Trip.id
') or exit("dot gen trips\n". $rodb->error);

foreach ($result as $tr) {
    echo "t".$tr["tid"] . ' [label="'. $tr["boat"] .'"];'."\n";
}

$result = $rodb->query('

SELECT CONCAT (FirstName," ", MemberId) as rower,MemberId as mid
FROM  Member WHERE id IN (
SELECT member_id FROM Trip,TripMember
WHERE YEAR(OutTime)=YEAR(NOW()) AND MONTH(OutTime)=MONTH(NOW()) AND Trip.id=TripMember.TripID)
') or exit("dot gen trips\n". $rodb->error);


echo 'node [
shape = "box"
width = "0.400000"
height = "0.400000"
color = "blue"
bgcolor = "pink"
	]
';
foreach ($result as $tr) {
    echo "r".$tr["mid"] . ' [label="'. $tr["rower"] .'"];'."\n";
}

$result = $rodb->query('
SELECT CONCAT (FirstName," ", MemberId) as rower, MemberId as mid,Boat.Name as boat,Trip.Meter as distance,TripType.Name as trip_type,Trip.id as tid
FROM Member, Boat,Trip,TripMember,TripType
WHERE Boat.id=Trip.BoatID AND
TripMember.TripID=Trip.id AND
YEAR(OutTime)=YEAR(NOW()) AND MONTH(OutTime)=MONTH(NOW()) AND
TripType.id=Trip.TripTypeID AND
Member.id=TripMember.member_id
ORDER BY Trip.id
') or exit("dot gen ". $rodb->error);
foreach ($result as $rt) {
    $tid=$rt["tid"];
    $tripl=$rt["boat"].$tid;
    $mid=$rt["mid"];
    echo " t$tid -- r$mid [len = 1]\n";
}
echo '}';
