<?php
require("inc/common.php");
include("inc/utils.php");

$s="
SELECT reservation.id,Boat.id as boat_id,Boat.Name as boat, TIME_FORMAT(start_time,'%H:%i') as start_time,start_date,TIME_FORMAT(end_time,'%H:%i') AS end_time,end_date,
    dayofweek,reservation.description,TripType.Name as triptype, TripType.id as triptype_id,purpose, configuration
    FROM reservation,Boat,TripType,reservation_configuration
    WHERE Boat.id=reservation.boat AND TripType.id=reservation.triptype AND
          (dayofweek>0 OR end_date>=DATE(NOW())) AND
          reservation_configuration.name=reservation.configuration AND
          reservation_configuration.selected>0
    ORDER BY boat,start_date,dayofweek,start_time
";

if ($sqldebug) {
    echo $s;
}
$result=$rodb->query($s) or dbErr($rodb,$res,"GET res");
output_rows($result);
$rodb->close();
