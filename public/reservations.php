<?php

$days=array('','mandag','tirsdag','onsdag','torsdag','fredag','lørdag','søndag');
set_include_path(get_include_path().':..');
ini_set('display_errors', 'On');
global $rodb;

$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
include(__DIR__."/publicutils.php");
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

$s='SELECT
      Boat.Name as båd,BoatType.Name as bådtype,
      TIME_FORMAT(start_time,"%H:%i") AS starttid,TIME_FORMAT(end_time,"%H:%i") AS sluttid,start_date AS startdato,end_date AS slutdato,weekday.name AS ugedag,
      TripType.Name AS turtype, Purpose as formål,configuration as conf
    FROM reservation,Boat,TripType,BoatType,weekday,reservation_configuration
    WHERE
       (dayofweek>0 OR end_date>=DATE(NOW())) AND
    Boat.id=reservation.boat AND TripType.id=triptype AND BoatType.name=Boat.boat_type AND weekday.no=reservation.dayofweek AND reservation_configuration.name=reservation.configuration
    ORDER BY start_date,dayofweek,Boat.Name,start_time';

$format=$_GET["format"] ?? "csv";
$q="bådreservationer";
$colormap=["turtype"=>["Motionsroning"=>"4444FF","Puls og program"=>"00FF00","Inriggerkaproning"=>"FF0000","Instruktion"=>"888844","Coastalroning"=>"00AAAA"]];
$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));
$captions="_auto";

process($result,$format,$q,$captions);

 // while ($row = $result->fetch_assoc()) {
 //     if ($row["dayofweek"] && $row["dayofweek"]>0) {
 //         $row["start_date"]="";
 //         $row["end_date"]="";
 //     }
 //     echo $row["boat"].",".$row["boattype"].",".$row["triptype"].",".$days[$row["dayofweek"]].",".$row["start_time"].",".$row["start_date"].",".$row["end_time"].",".$row["end_date"]."\n";
 // }
