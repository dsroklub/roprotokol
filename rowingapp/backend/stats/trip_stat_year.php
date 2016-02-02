<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
header('Content-type: application/json');

$s="SELECT YEAR(Trip.OutTime) as year,TripType.name,CAST(Sum(Meter) AS UNSIGNED) AS distance, COUNT('x') as trips FROM Trip,TripType WHERE Trip.TripTypeID=TripType.id GROUP BY TripType.name, year ORDER BY year,TripType.name";
#echo $s;
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result();
     echo '[';
     $rn=1;
     while ($row = $result->fetch_assoc()) {
         if ($rn>1) echo ',';
         echo json_encode($row);
         $rn=$rn+1;
     }
     echo ']';     
     $stmt->close(); 
 } 
$rodb->close();
?> 
