<?php
include("inc/common.php");
header('Content-type: application/json');

$boatclause="";
if (isset($_GET["boattype"])) {
    $boattype=$_GET["boattype"];
    if ($boattype=="any") {
        $boatclause="";
    } elseif ($boattype=="kayak") {
        $boatclause=" AND (BoatType.Category=1) ";
    } elseif ($boattype=="rowboat") {
        $boatclause=" AND (BoatType.Category=2)";
    } else {
        error_log('unknown boattype: '.$boattype);
        echo "unknown boattype: ".$boattype;
        exit(0);
    }
}


$s="SELECT Boat.id,Boat.Name AS boatname, BoatType.Name AS boat_type, CAST(Sum(Meter) AS UNSIGNED) AS distance, Count(Trip.id) AS num_trips
FROM (BoatType INNER JOIN Boat ON BoatType.id = Boat.BoatType) LEFT JOIN Trip ON Boat.id = Trip.BoatID
WHERE Year(OutTime)=? ". $boatclause .
    " GROUP BY Boat.Name, BoatType.Name, Boat.id
    ORDER BY distance desc";
    
if ($sqldebug) echo $s;
if ($stmt = $rodb->prepare($s)) {
     $stmt->bind_param("s", $season);
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
