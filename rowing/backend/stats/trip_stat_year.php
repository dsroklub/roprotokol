<?php
set_include_path(get_include_path().':..');
include("inc/common.php");


if (isset($_GET["allboats"])) {
    $qboats="";
} else {
    $qboats=" AND Category=2 ";
}

if (isset($_GET["km"])) {
    $km="/1000";
} else {
    $km=" ";
}


$s="SELECT YEAR(Trip.OutTime) as year,TripType.tripstat_name as name,CAST(Sum(Meter)$km AS UNSIGNED) AS distance, COUNT('x') as trips 
    FROM Trip,TripType,Boat,BoatType
    WHERE Trip.TripTypeID=TripType.id AND Trip.BoatID=Boat.id $qboats AND Boat.boat_type=BoatType.Name
    AND Trip.OutTime IS NOT NULL
    GROUP BY TripType.name, year,TripType.tripstat_name
    ORDER BY year,TripType.tripstat_name";

if ($sqldebug) {
    echo $s;
}

if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result();
     process($result,$output,"turtypestatistik",array("season","turtype","km","ture"));
     $stmt->close(); 
 } 
$rodb->close();
