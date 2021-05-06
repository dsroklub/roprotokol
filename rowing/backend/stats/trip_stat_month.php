<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
if (isset($_GET["km"])) {
    $km="/1000";
} else {
    $km=" ";
}

$rodb->query("SET lc_time_names = 'da_DK'");
$s="SELECT YEAR(Trip.OutTime) as år,MONTHNAME(Trip.OutTime) as måned,CASE WHEN category=2 THEN 'robåd' WHEN category=1 THEN 'kajak' WHEN category=3 THEN 'motor' END as kategori,CAST(Sum(Meter)$km AS UNSIGNED) AS distance, COUNT('x') as ture
    FROM Trip,TripType,Boat,BoatType
    WHERE Trip.TripTypeID=TripType.id AND Trip.BoatID=Boat.id AND Boat.boat_type=BoatType.Name AND
     YEAR(NOW())-YEAR(Trip.OutTime)<6 AND
    Trip.OutTime IS NOT NULL
    GROUP BY år,måned,BoatType.category
    ORDER BY BoatType.category,YEAR(Trip.OutTime),MONTH(Trip.OutTime)";

if ($sqldebug) {
    echo $s;
}

$result= $rodb->query($s) or dbErr($rodb,$res,"exe");
process($result,$output,"månedsstatistik","_auto");
$rodb->close();
