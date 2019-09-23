<?php
include("../../../rowing/backend/inc/common.php");
include("utils.php");
header('Content-Disposition: filename="sumgraph.json"');

$data=["nodes"=>[],"links"=>[]];

$timeClause=" YEAR(OutTime)=YEAR(NOW()) AND MONTH(OutTime)=MONTH(NOW())";

$result = $rodb->query("
SELECT FirstName as rower,MemberId as mid
FROM  Member WHERE id IN (
SELECT member_id FROM Trip,TripMember
WHERE $timeClause AND Trip.id=TripMember.TripID) AND
NOT MemberID LIKE 'g%'
") or exit("dot gen trips\n". $rodb->error);


foreach ($result as $i=>$tr) {
    $rl[$tr["mid"]]=$i;
    $rower=["name"=>explode(' ',$tr["rower"])[0]];
    if ($tr["mid"]==$cuser) {
        $rower["me"]=$tr["mid"];
    }
    $data["nodes"][]=$rower;
}

$result = $rodb->query("
SELECT m1.MemberId as m1, m2.MemberId as m2, Sum(Trip.Meter) as distance, GROUP_CONCAT(DISTINCT TripType.Name) as triptypes
FROM Member m1, Member m2,Trip,TripMember tm1,TripMember tm2,TripType
WHERE 
TripType.id=Trip.TripTypeID AND
tm1.TripID=Trip.id AND tm2.TripID=Trip.id AND
m1.id=tm1.member_id AND m2.id=tm2.member_id AND
m1.id<m2.id AND $timeClause 
GROUP BY m1.id,m2.id
") or exit("dot gen ". $rodb->error);

$triptypecolors=["blandet"=>"blue","Inriggerkaproning"=>"red","Motionsroning"=>"green","Puls og program"=>"brown","Langtur"=>"pink","Racerkanin"=>"grey","Costalroning" => "cyan3","Instruktion"=>"bisque","Kajakmotionsroning"=>"darkorange"];
foreach ($result as $rt) {
    if (isset($rl[$rt["m1"]]) and isset($rl[$rt["m2"]])) {
        $tooltip=$rt["triptypes"];
        $c=$triptypecolors[$tooltip]??$triptypecolors["blandet"];
        $dist=round($rt["distance"]/1000);
        $len=0.2+10000/(300 +$rt["distance"]);
        $data["links"][]=["source"=>$rl[$rt["m1"]],"target"=>$rl[$rt["m2"]],"tooltip"=>$tooltip, "color"=>$c,"label"=>"".$dist ];
    }
}


$data["constraints"]=[["axis"=>"y","left"=>0,"right"=>1,"gap"=>25]];

//echo 'Legend [shape=none, margin=0, label=<<table border="0" cellborder="1" cellspacing="0" cellpadding="4">';
//foreach ($triptypecolors as $tt => $tc) {
//    echo "<tr><td bgcolor=\"$tc\">$tt</td></tr>\n";
//}

echo json_encode($data,JSON_PRETTY_PRINT);
