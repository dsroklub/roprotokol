<?php
include("inc/common.php");

$season=date('Y');


$boatclause="";
if (isset($_GET["boattype"])) {
    $boattype=$_GET["boattype"];
    if ($boattype=="any") {
        $boatclause="";
    } elseif ($boattype=="kayak") {
        $boatclause=" AND ((Gruppe.FK_BådKategoriID)=1) ";
    } elseif ($boattype=="rowboat") {
        $boatclause=" AND ((Gruppe.FK_BådKategoriID)=2)";
    } else {
        error_log('unknown boattype: '.$boattype);
        echo "unknown boattype: ".$boattype;
        exit(0);
    }
}


$s="SELECT Båd.Navn AS boatname, Gruppe.Navn AS boat_type, Sum(ROUND(Meter/100)/10) AS distance, Count(Trip.TripID) AS num_trips
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Trip ON Båd.BådID = Trip.BoatID
WHERE Year(OutTime)=".$season ." ". $boatclause .
    " GROUP BY Båd.Navn, Gruppe.Navn";
    
//    echo $s;
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result();
     echo '[';
     $rn=1;
     while ($row = $result->fetch_assoc()) {
         if ($rn>1) echo ',';
         $row['rank']=$rn;
         echo json_encode($row);
         $rn=$rn+1;
     }
     echo ']';     
     $stmt->close(); 
 } 
$rodb->close();
?> 
