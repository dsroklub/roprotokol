<?php

$days=array('','mandag','tirsdag','onsdag','torsdag','fredag','lørdag','søndag');
set_include_path(get_include_path().':..');
header('Content-type: text/csv');
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');

$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

header('Content-Disposition: filename="bådreservationer.csv"');
$s='SELECT Boat.Name as boat,start_time,end_time,start_date,end_date,dayofweek,TripType.Name as triptype, BoatType.Name as boattype
    FROM reservation,Boat,TripType,BoatType 
    WHERE Boat.id=boat AND TripType.id=triptype AND BoatType.id=BoatType
    ORDER BY Boat.Name,dayofweek,start_time';

$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));;
 while ($row = $result->fetch_assoc()) {
     if ($row["dayofweek"] && $row["dayofweek"]>0) {
         $row["start_date"]="";
         $row["end_date"]="";
     }
     echo $row["boat"].",".$row["boattype"].",".$row["triptype"].",".$days[$row["dayofweek"]].",".$row["start_time"].",".$row["start_date"].",".$row["end_time"].",".$row["end_date"]."\n";
 }
?> 
