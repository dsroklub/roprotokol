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
$s='SELECT Boat.Name as boat, GROUP_CONCAT(TIME_FORMAT(start_time,"%H:%i"),"-",TIME_FORMAT(end_time,"%H:%i")," ",TripType.Name SEPARATOR "/") as reservation,dayofweek
    FROM reservation,Boat,TripType,BoatType 
    WHERE Boat.id=boat AND TripType.id=triptype AND BoatType.id=BoatType AND dayofweek>0
    GROUP BY boat,dayofweek
    ORDER BY Boat.Name,dayofweek,start_time';

$result=$rodb->query($s) or die("Error in reservations query: " . mysqli_error($rodb));;
echo "Båd,mandag,tirsdag,onsdag,torsdag,fredag,lørdag,søndag";
$row = $result->fetch_assoc();
 while ($row) {
     $boat=$row["boat"];
     echo "\n".$boat;
     for ($d = 1; $d <= 7; $d++) {
         echo ",";
         if ($d==$row["dayofweek"] and $boat==$row["boat"]) {
             echo $row["reservation"];
             $row = $result->fetch_assoc();
         }
     }
 }
?> 
